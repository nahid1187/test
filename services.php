<?php
require '../config/db.php';
$uid=$_SESSION['uid']??0;
if(!$uid){echo json_encode(['ok'=>false,'msg'=>'Not logged in']);exit;}

function getSid($pdo,$uid){$s=$pdo->prepare("SELECT id FROM salons WHERE owner_id=?");$s->execute([$uid]);$r=$s->fetch();return $r?$r['id']:null;}

switch($action){
case 'get':
    $sid=getSid($pdo,$uid);
    if(!$sid){echo json_encode(['ok'=>true,'services'=>[]]);exit;}
    $s=$pdo->prepare("SELECT id,name,price FROM services WHERE salon_id=?");$s->execute([$sid]);
    echo json_encode(['ok'=>true,'services'=>$s->fetchAll()]);
    break;
case 'add':
    $nm=trim($d['name']??'');$pr=(float)($d['price']??0);
    if(!$nm||$pr<=0){echo json_encode(['ok'=>false,'msg'=>'Name & price required']);exit;}
    $sid=getSid($pdo,$uid);
    if(!$sid){echo json_encode(['ok'=>false,'msg'=>'Create salon profile first']);exit;}
    $pdo->prepare("INSERT INTO services(salon_id,name,price)VALUES(?,?,?)")->execute([$sid,$nm,$pr]);
    echo json_encode(['ok'=>true,'msg'=>'Added!']);
    break;
case 'delete':
    $id=(int)($d['service_id']??0);$sid=getSid($pdo,$uid);
    $pdo->prepare("DELETE FROM services WHERE id=? AND salon_id=?")->execute([$id,$sid]);
    echo json_encode(['ok'=>true]);
    break;
default: echo json_encode(['ok'=>false,'msg'=>'Unknown']);
}
