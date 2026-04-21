<?php
/**
 * Admin Password Reset Tool
 * Run this once in browser: http://localhost/salon_final/fix_admin.php
 * DELETE this file immediately after use!
 */
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=salon_db;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password=? WHERE email='admin@salon.com' AND role='admin'");
    $stmt->execute([$hash]);
    if ($stmt->rowCount() > 0) {
        echo "<h2 style='color:green'>✅ Success! Admin password reset to: admin123</h2>";
        echo "<p>Secret Code: <strong>salon@secure123</strong></p>";
        echo "<p><strong>⚠️ Please delete this file (fix_admin.php) now!</strong></p>";
        echo "<p><a href='admin-login.html'>Go to Admin Login →</a></p>";
    } else {
        echo "<h2 style='color:orange'>⚠️ No admin user found. Inserting new admin...</h2>";
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES ('Admin','admin@salon.com',?,'admin')")
            ->execute([$hash]);
        echo "<h2 style='color:green'>✅ Admin created! Password: admin123</h2>";
        echo "<p>Secret Code: <strong>salon@secure123</strong></p>";
        echo "<p><strong>⚠️ Please delete this file (fix_admin.php) now!</strong></p>";
        echo "<p><a href='admin-login.html'>Go to Admin Login →</a></p>";
    }
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<p>Check that XAMPP MySQL is running and database 'salon_db' exists.</p>";
}
