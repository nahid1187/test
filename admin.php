<?php
require '../config/db.php';
if(($_SESSION['role']??'')!=='admin'){echo json_encode(['ok'=>false,'msg'=>'Forbidden']);exit;}

switch($action){
case 'stats':
    echo json_encode(['ok'=>true,'stats'=>[
        'users'        =>(int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role!='admin'")->fetch()['c'],
        'salons'       =>(int)$pdo->query("SELECT COUNT(*) c FROM salons")->fetch()['c'],
        'appointments' =>(int)$pdo->query("SELECT COUNT(*) c FROM appointments")->fetch()['c'],
    ]]);break;

case 'get_users':
    $s=$pdo->query("SELECT id,name,email,role,created_at FROM users WHERE role!='admin' ORDER BY created_at DESC");
    echo json_encode(['ok'=>true,'users'=>$s->fetchAll()]);break;

case 'delete_user':
    $pdo->prepare("DELETE FROM users WHERE id=? AND role!='admin'")->execute([(int)($d['user_id']??0)]);
    echo json_encode(['ok'=>true]);break;

case 'get_salons':
    $s=$pdo->query("SELECT s.id,s.salon_name,s.location,s.rating,u.name owner_name,u.email owner_email FROM salons s JOIN users u ON u.id=s.owner_id ORDER BY s.id DESC");
    echo json_encode(['ok'=>true,'salons'=>$s->fetchAll()]);break;

case 'delete_salon':
    $pdo->prepare("DELETE FROM salons WHERE id=?")->execute([(int)($d['salon_id']??0)]);
    echo json_encode(['ok'=>true]);break;

case 'get_appointments':
    $s=$pdo->query("SELECT a.id,a.appt_date,a.appt_time,a.status,s.salon_name,u.name customer_name,sv.name service_name,b.name barber_name
        FROM appointments a JOIN salons s ON s.id=a.salon_id JOIN users u ON u.id=a.customer_id JOIN services sv ON sv.id=a.service_id JOIN barbers b ON b.id=a.barber_id
        ORDER BY a.created_at DESC");
    echo json_encode(['ok'=>true,'appointments'=>$s->fetchAll()]);break;

case 'delete_appointment':
    $pdo->prepare("DELETE FROM appointments WHERE id=?")->execute([(int)($d['appointment_id']??0)]);
    echo json_encode(['ok'=>true]);break;

default: echo json_encode(['ok'=>false,'msg'=>'Unknown']);
}
