<?php
require '../config/db.php';
$uid=$_SESSION['uid']??0; $role=$_SESSION['role']??'';
if(!$uid){echo json_encode(['ok'=>false,'msg'=>'Not logged in']);exit;}

function slots(){$s=[];for($h=10;$h<20;$h++){$s[]=sprintf('%02d:00 - %02d:30',$h,$h);$s[]=sprintf('%02d:30 - %02d:00',$h,$h+1);}return $s;}

switch($action){
case 'book':
    $sid=(int)($d['salon_id']??0);$svc=(int)($d['service_id']??0);
    $bid=(int)($d['barber_id']??0);$dt=$d['date']??'';$tm=$d['time']??'';
    if(!$sid||!$svc||!$bid||!$dt||!$tm){echo json_encode(['ok'=>false,'msg'=>'All fields required']);exit;}
    $c=$pdo->prepare("SELECT id FROM appointments WHERE salon_id=? AND barber_id=? AND appt_date=? AND appt_time=? AND status NOT IN('Cancelled','Rejected')");
    $c->execute([$sid,$bid,$dt,$tm]);
    if($c->fetch()){echo json_encode(['ok'=>false,'msg'=>'Slot already booked']);exit;}
    $pdo->prepare("INSERT INTO appointments(customer_id,salon_id,service_id,barber_id,appt_date,appt_time)VALUES(?,?,?,?,?,?)")
        ->execute([$uid,$sid,$svc,$bid,$dt,$tm]);
    echo json_encode(['ok'=>true,'msg'=>'Booked successfully!']);
    break;

case 'get_mine':
    $s=$pdo->prepare("SELECT a.id,a.appt_date,a.appt_time,a.status,s.salon_name,sv.name service_name,b.name barber_name
        FROM appointments a JOIN salons s ON s.id=a.salon_id JOIN services sv ON sv.id=a.service_id JOIN barbers b ON b.id=a.barber_id
        WHERE a.customer_id=? ORDER BY a.appt_date DESC");
    $s->execute([$uid]);echo json_encode(['ok'=>true,'appointments'=>$s->fetchAll()]);
    break;

case 'get_salon':
    $sq=$pdo->prepare("SELECT id FROM salons WHERE owner_id=?");$sq->execute([$uid]);$sl=$sq->fetch();
    if(!$sl){echo json_encode(['ok'=>true,'appointments'=>[]]);exit;}
    $s=$pdo->prepare("SELECT a.id,a.appt_date,a.appt_time,a.status,u.name customer_name,u.email customer_email,sv.name service_name,b.name barber_name
        FROM appointments a JOIN users u ON u.id=a.customer_id JOIN services sv ON sv.id=a.service_id JOIN barbers b ON b.id=a.barber_id
        WHERE a.salon_id=? ORDER BY a.appt_date DESC");
    $s->execute([$sl['id']]);echo json_encode(['ok'=>true,'appointments'=>$s->fetchAll()]);
    break;

case 'update_status':
    $aid=(int)($d['appointment_id']??0);$st=$d['status']??'';
    if(!in_array($st,['Accepted','Rejected','Completed'])){echo json_encode(['ok'=>false,'msg'=>'Invalid status']);exit;}
    $pdo->prepare("UPDATE appointments SET status=? WHERE id=?")->execute([$st,$aid]);
    echo json_encode(['ok'=>true]);
    break;

case 'cancel':
    $aid=(int)($d['appointment_id']??0);
    $pdo->prepare("UPDATE appointments SET status='Cancelled' WHERE id=? AND customer_id=?")->execute([$aid,$uid]);
    echo json_encode(['ok'=>true]);
    break;

case 'get_all':
    if($role!=='admin'){echo json_encode(['ok'=>false,'msg'=>'Forbidden']);exit;}
    $s=$pdo->query("SELECT a.id,a.appt_date,a.appt_time,a.status,s.salon_name,u.name customer_name,sv.name service_name,b.name barber_name
        FROM appointments a JOIN salons s ON s.id=a.salon_id JOIN users u ON u.id=a.customer_id JOIN services sv ON sv.id=a.service_id JOIN barbers b ON b.id=a.barber_id
        ORDER BY a.created_at DESC");
    echo json_encode(['ok'=>true,'appointments'=>$s->fetchAll()]);
    break;

case 'delete':
    if($role!=='admin'){echo json_encode(['ok'=>false,'msg'=>'Forbidden']);exit;}
    $pdo->prepare("DELETE FROM appointments WHERE id=?")->execute([(int)($d['appointment_id']??0)]);
    echo json_encode(['ok'=>true]);
    break;

case 'get_slots':
    $sid=(int)($d['salon_id']??$_GET['salon_id']??0);
    $bid=(int)($d['barber_id']??$_GET['barber_id']??0);
    $dt=$d['date']??$_GET['date']??'';
    $s=$pdo->prepare("SELECT appt_time FROM appointments WHERE salon_id=? AND barber_id=? AND appt_date=? AND status NOT IN('Cancelled','Rejected')");
    $s->execute([$sid,$bid,$dt]);$booked=array_column($s->fetchAll(),'appt_time');
    $all=array_map(fn($sl)=>['time'=>$sl,'available'=>!in_array($sl,$booked)],slots());
    echo json_encode(['ok'=>true,'slots'=>$all]);
    break;

case 'rate':
    $sid=(int)($d['salon_id']??0);$rt=(int)($d['rating']??0);
    if($rt<1||$rt>5){echo json_encode(['ok'=>false,'msg'=>'Rating 1-5']);exit;}
    $pdo->prepare("INSERT INTO reviews(salon_id,customer_id,rating)VALUES(?,?,?) ON DUPLICATE KEY UPDATE rating=VALUES(rating)")
        ->execute([$sid,$uid,$rt]);
    $avg=$pdo->prepare("SELECT AVG(rating) avg FROM reviews WHERE salon_id=?");$avg->execute([$sid]);
    $ar=round($avg->fetch()['avg'],1);
    $pdo->prepare("UPDATE salons SET rating=? WHERE id=?")->execute([$ar,$sid]);
    echo json_encode(['ok'=>true,'avg'=>$ar]);
    break;

default: echo json_encode(['ok'=>false,'msg'=>'Unknown action']);
}
