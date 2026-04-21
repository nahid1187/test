<?php
// config/db.php — Database connection
// CHANGE root password below if your MySQL has one
if(session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if($_SERVER['REQUEST_METHOD']==='OPTIONS'){http_response_code(200);exit;}

try {
    $pdo = new PDO(
        "mysql:host=localhost;
        dbname=salon_db;charset=utf8mb4",
        "root",   // ← your MySQL username
        "",       // ← your MySQL password (empty by default in XAMPP)
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch(PDOException $e){
    http_response_code(500);
    die(json_encode(['ok'=>false,'msg'=>'DB Error: '.$e->getMessage()]));
}

$d      = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $d['action'] ?? $_GET['action'] ?? '';
