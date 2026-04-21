<?php
require '../config/db.php';
$uid = $_SESSION['uid']??0; $role=$_SESSION['role']??'';

function salonId($pdo,$uid){
    $s=$pdo->prepare("SELECT id FROM salons WHERE owner_id=?");$s->execute([$uid]);
    $r=$s->fetch();return $r?$r['id']:null;
}

switch($action){
case 'get_all':
    $rows=$pdo->query("SELECT s.*,u.email owner_email FROM salons s JOIN users u ON u.id=s.owner_id")->fetchAll();
    foreach($rows as &$row){
        $sid=$row['id'];
        $sv=$pdo->prepare("SELECT id,name,price FROM services WHERE salon_id=?");$sv->execute([$sid]);$row['services']=$sv->fetchAll();
        $b=$pdo->prepare("SELECT id,name FROM barbers WHERE salon_id=?");$b->execute([$sid]);$row['barbers']=$b->fetchAll();
        $r=$pdo->prepare("SELECT AVG(rating) avg FROM reviews WHERE salon_id=?");$r->execute([$sid]);$row['rating']=round($r->fetch()['avg']??0,1);
    }
    echo json_encode(['ok'=>true,'salons'=>$rows]);
    break;

case 'get_mine':
    if(!$uid){echo json_encode(['ok'=>false,'msg'=>'Not logged in']);exit;}
    $s=$pdo->prepare("SELECT * FROM salons WHERE owner_id=?");$s->execute([$uid]);$salon=$s->fetch();
    if(!$salon){echo json_encode(['ok'=>true,'salon'=>null]);exit;}
    $sid=$salon['id'];
    $sv=$pdo->prepare("SELECT id,name,price FROM services WHERE salon_id=?");$sv->execute([$sid]);$salon['services']=$sv->fetchAll();
    $b=$pdo->prepare("SELECT id,name FROM barbers WHERE salon_id=?");$b->execute([$sid]);$salon['barbers']=$b->fetchAll();
    echo json_encode(['ok'=>true,'salon'=>$salon]);
    break;

case 'save_profile':
    if(!$uid){echo json_encode(['ok'=>false,'msg'=>'Not logged in']);exit;}
    $nm=trim($d['salon_name']??''); $loc=trim($d['location']??'');
    if(!$nm){echo json_encode(['ok'=>false,'msg'=>'Name required']);exit;}
    $s=$pdo->prepare("SELECT id FROM salons WHERE owner_id=?");$s->execute([$uid]);
    if($s->fetch()) $pdo->prepare("UPDATE salons SET salon_name=?,location=? WHERE owner_id=?")->execute([$nm,$loc,$uid]);
    else $pdo->prepare("INSERT INTO salons(owner_id,salon_name,location)VALUES(?,?,?)")->execute([$uid,$nm,$loc]);
    echo json_encode(['ok'=>true,'msg'=>'Saved!']);
    break;

case 'stats':
    if(!$uid){echo json_encode(['ok'=>false]);exit;}
    $sid=salonId($pdo,$uid);
    if(!$sid){echo json_encode(['ok'=>true,'stats'=>['appointments'=>0,'services'=>0,'barbers'=>0]]);exit;}
    $a=$pdo->prepare("SELECT COUNT(*) c FROM appointments WHERE salon_id=?");$a->execute([$sid]);
    $sv=$pdo->prepare("SELECT COUNT(*) c FROM services WHERE salon_id=?");$sv->execute([$sid]);
    $b=$pdo->prepare("SELECT COUNT(*) c FROM barbers WHERE salon_id=?");$b->execute([$sid]);
    echo json_encode(['ok'=>true,'stats'=>['appointments'=>(int)$a->fetch()['c'],'services'=>(int)$sv->fetch()['c'],'barbers'=>(int)$b->fetch()['c']]]);
    break;

case 'customer_stats':
    $sc=$pdo->query("SELECT COUNT(*) c FROM salons")->fetch()['c'];
    $bc=$pdo->query("SELECT COUNT(*) c FROM barbers")->fetch()['c'];
    echo json_encode(['ok'=>true,'salons'=>(int)$sc,'barbers'=>(int)$bc]);
    break;

case 'delete':
    if($role!=='admin'){echo json_encode(['ok'=>false,'msg'=>'Forbidden']);exit;}
    $pdo->prepare("DELETE FROM salons WHERE id=?")->execute([(int)($d['salon_id']??0)]);
    echo json_encode(['ok'=>true]);
    break;

default: echo json_encode(['ok'=>false,'msg'=>'Unknown action']);
}
