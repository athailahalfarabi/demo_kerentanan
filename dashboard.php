<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Demo Keamanan Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    🛡️ <span>Demo Keamanan Data</span>
                </div>
                <div class="user-info">
                    <span>User: <strong><?php echo htmlspecialchars($username); ?></strong> (<?php echo htmlspecialchars($role); ?>)</span>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="navigation-tabs">
            <a href="dashboard.php" class="tab-btn active">Dashboard</a>
            <a href="vulnerable.php" class="tab-btn tab-vuln">🔓 Vulnerable</a>
            <a href="safe.php" class="tab-btn tab-safe">🔒 Safe</a>
        </div>

        <div class="content">
            <h2 class="page-title">Dashboard Keamanan Data</h2>
            
            <div class="card-grid">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3>SQL Injection</h3>
                        <span class="icon">⚠️</span>
                    </div>
                    <p>Login bypass dengan input khusus pada query SQL</p>
                    <a href="login.php" class="btn btn-sm btn-outline">Lihat Demo</a>
                </div>

                <div class="card card-warning">
                    <div class="card-header">
                        <h3>Cross-Site Scripting</h3>
                        <span class="icon">🔥</span>
                    </div>
                    <p>XSS vulnerability pada input form tanpa sanitasi</p>
                    <a href="vulnerable.php" class="btn btn-sm btn-outline">Lihat Demo</a>
                </div>

                <div class="card card-orange">
                    <div class="card-header">
                        <h3>File Upload</h3>
                        <span class="icon">📁</span>
                    </div>
                    <p>Upload file tanpa validasi tipe dan ukuran</p>
                    <a href="vulnerable.php" class="btn btn-sm btn-outline">Lihat Demo</a>
                </div>

                <div class="card card-purple">
                    <div class="card-header">
                        <h3>Broken Access Control</h3>
                        <span class="icon">🚨</span>
                    </div>
                    <p>Akses ke resource tanpa authorization yang proper</p>
                    <a href="vulnerable.php" class="btn btn-sm btn-outline">Lihat Demo</a>
                </div>
            </div>

            <div class="info-box">
                <h3>📚 Tentang Demo Ini</h3>
                <p>Website ini dibuat untuk tujuan edukasi keamanan aplikasi web. Terdapat 4 jenis kerentanan utama yang didemonstrasikan:</p>
                <ul>
                    <li><strong>SQL Injection:</strong> Celah pada query database yang memungkinkan attacker memanipulasi query</li>
                    <li><strong>Cross-Site Scripting (XSS):</strong> Celah yang memungkinkan injeksi script berbahaya</li>
                    <li><strong>File Upload Vulnerability:</strong> Celah pada upload file tanpa validasi proper</li>
                    <li><strong>Broken Access Control:</strong> Celah authorization yang memungkinkan akses tidak sah</li>
                </ul>
                <p class="mt-2"><strong>Catatan:</strong> Gunakan halaman "Vulnerable" untuk melihat implementasi yang rentan, dan "Safe" untuk melihat implementasi yang aman.</p>
            </div>
        </div>
    </div>
</body>
</html>