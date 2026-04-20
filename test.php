<?php
// ================================================
//  test.php - Run this to check everything works
//  Visit: http://localhost/salon/test.php
// ================================================
?>
<!DOCTYPE html>
<html>
<head>
<title>Salon System - Diagnostic</title>
<style>
body{font-family:Arial;padding:30px;background:#f4f6f8;}
h2{color:#333;}
.ok  {background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin:8px 0;}
.err {background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin:8px 0;}
.info{background:#d1ecf1;color:#0c5460;padding:10px;border-radius:6px;margin:8px 0;}
</style>
</head>
<body>
<h2>🔍 Salon System Diagnostic</h2>

<?php
// 1. PHP Check
echo '<div class="ok">✅ PHP is working — Version: ' . phpversion() . '</div>';

// 2. Session Check
session_start();
$_SESSION['test'] = 'ok';
echo isset($_SESSION['test'])
    ? '<div class="ok">✅ Sessions are working</div>'
    : '<div class="err">❌ Sessions NOT working</div>';

// 3. DB Connection Check
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=salon_db;charset=utf8mb4",
        "root", "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo '<div class="ok">✅ Database connected — salon_db found</div>';

    // 4. Tables Check
    $tables = ['users','salons','services','barbers','appointments','reviews'];
    foreach($tables as $t){
        $r = $pdo->query("SHOW TABLES LIKE '$t'")->fetch();
        echo $r
            ? "<div class=\"ok\">✅ Table <strong>$t</strong> exists</div>"
            : "<div class=\"err\">❌ Table <strong>$t</strong> is MISSING — re-import database.sql</div>";
    }

    // 5. Admin user check
    $s = $pdo->query("SELECT id,name,email,role FROM users WHERE role='admin'")->fetch();
    echo $s
        ? "<div class=\"ok\">✅ Admin user found: {$s['email']}</div>"
        : '<div class="err">❌ No admin user found — re-import database.sql</div>';

} catch(PDOException $e){
    echo '<div class="err">❌ DB Connection FAILED: ' . $e->getMessage() . '<br><br>
        <strong>Fix:</strong> Open <code>config/db.php</code> and check your MySQL username/password.<br>
        Default XAMPP = username: <code>root</code>, password: <em>(empty)</em>
    </div>';
}

// 6. File paths check
$files = [
    'api/auth.php','api/salons.php','api/services.php',
    'api/barbers.php','api/appointments.php','api/admin.php',
    'config/db.php','assets/js/api.js'
];
foreach($files as $f){
    echo file_exists(__DIR__.'/'.$f)
        ? "<div class=\"ok\">✅ File exists: <code>$f</code></div>"
        : "<div class=\"err\">❌ MISSING file: <code>$f</code></div>";
}

// 7. URL info
echo '<div class="info">📍 Current URL path: <code>' . $_SERVER['REQUEST_URI'] . '</code></div>';
echo '<div class="info">📁 Server root: <code>' . $_SERVER['DOCUMENT_ROOT'] . '</code></div>';
echo '<div class="info">📂 This file is at: <code>' . __DIR__ . '</code></div>';
?>

<hr>
<h3>✅ If everything above is green — your setup is correct!</h3>
<p>Visit <a href="index.html">index.html</a> to use the system.</p>
<p style="color:red"><strong>Delete this file after testing!</strong></p>
</body>
</html>
