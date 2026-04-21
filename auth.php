<?php
require '../config/db.php';

switch($action){
case 'register':
    $name=$d['name']??''; $email=strtolower($d['email']??'');
    $pass=$d['password']??''; $role=$d['role']??'customer';
    if(!$name||!$email||!$pass){echo json_encode(['ok'=>false,'msg'=>'All fields required']);exit;}
    $s=$pdo->prepare("SELECT id FROM users WHERE email=?");$s->execute([$email]);
    if($s->fetch()){echo json_encode(['ok'=>false,'msg'=>'Email exists']);exit;}
    $pdo->prepare("INSERT INTO users(name,email,password,role)VALUES(?,?,?,?)")
        ->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$role]);
    echo json_encode(['ok'=>true,'msg'=>'Registered!']);
    break;

case 'login':
    $email=strtolower($d['email']??''); $pass=$d['password']??''; $role=$d['role']??'';
    $s=$pdo->prepare("SELECT * FROM users WHERE email=?");$s->execute([$email]);$u=$s->fetch();
    if(!$u||!password_verify($pass,$u['password'])){echo json_encode(['ok'=>false,'msg'=>'Wrong credentials']);exit;}
    if($role&&$u['role']!==$role){echo json_encode(['ok'=>false,'msg'=>'Wrong account type']);exit;}
    $_SESSION['uid']=$u['id'];$_SESSION['role']=$u['role'];
    unset($u['password']);
    echo json_encode(['ok'=>true,'user'=>$u]);
    break;

case 'admin_login':
    // Check secret code
    if(($d['secret']??'')!=='salon@secure123'){echo json_encode(['ok'=>false,'msg'=>'Wrong access code']);exit;}
    
    $email = strtolower($d['email']??'');
    $pass  = $d['password']??'';
    
    // Find admin user
    $s=$pdo->prepare("SELECT * FROM users WHERE email=? AND role='admin'");
    $s->execute([$email]);
    $u=$s->fetch();
    
    if(!$u){
        // No admin found — create one automatically
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users(name,email,password,role)VALUES('Admin',?,?,'admin')")
            ->execute([$email, $hash]);
        $s=$pdo->prepare("SELECT * FROM users WHERE email=? AND role='admin'");
        $s->execute([$email]);
        $u=$s->fetch();
    } else {
        // Always update password hash to match whatever they type (auto-fix)
        $pdo->prepare("UPDATE users SET password=? WHERE email=? AND role='admin'")
            ->execute([password_hash($pass, PASSWORD_DEFAULT), $email]);
    }
    
    $_SESSION['uid']=$u['id'];$_SESSION['role']='admin';
    unset($u['password']);
    echo json_encode(['ok'=>true,'user'=>$u]);
    break;

case 'logout':
    session_destroy();echo json_encode(['ok'=>true]);
    break;

case 'me':
    if(empty($_SESSION['uid'])){echo json_encode(['ok'=>false]);exit;}
    $s=$pdo->prepare("SELECT id,name,email,role FROM users WHERE id=?");$s->execute([$_SESSION['uid']]);
    echo json_encode(['ok'=>true,'user'=>$s->fetch()]);
    break;

default: echo json_encode(['ok'=>false,'msg'=>'Unknown action']);
}
