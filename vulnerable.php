<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$message = '';
$upload_message = '';

// VULNERABLE: XSS - No sanitization
if (isset($_POST['submit_message_vuln'])) {
    $msg = $_POST['message'];
    
    // VULNERABLE: Direct insertion without escaping
    $query = "INSERT INTO messages (user_id, message) VALUES ($user_id, '$msg')";
    mysqli_query($conn, $query);
    $message = "Pesan berhasil disimpan!";
}

// VULNERABLE: File Upload - No validation
if (isset($_POST['upload_vuln'])) {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $filename = $file['name'];
        $tmp_name = $file['tmp_name'];
        $filesize = $file['size'];
        $filetype = $file['type'];
        
        // VULNERABLE: No file type validation
        $upload_path = "uploads/" . $filename;
        
        if (move_uploaded_file($tmp_name, $upload_path)) {
            // Insert to database
            $query = "INSERT INTO uploaded_files (user_id, filename, filepath, filesize, filetype) 
                     VALUES ($user_id, '$filename', '$upload_path', $filesize, '$filetype')";
            mysqli_query($conn, $query);
            $upload_message = "File '$filename' berhasil diupload! (Tidak ada validasi)";
        } else {
            $upload_message = "Upload gagal!";
        }
    }
}

// Get messages (for XSS demo)
$messages_query = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC LIMIT 10";
$messages_result = mysqli_query($conn, $messages_query);

// Get uploaded files
$files_query = "SELECT * FROM uploaded_files WHERE user_id = $user_id ORDER BY uploaded_at DESC";
$files_result = mysqli_query($conn, $files_query);

// VULNERABLE: Broken Access Control - Anyone can access admin panel
$all_users_query = "SELECT id, username, email, role FROM users";
$all_users_result = mysqli_query($conn, $all_users_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable - Demo Keamanan Data</title>
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
            <a href="dashboard.php" class="tab-btn">Dashboard</a>
            <a href="vulnerable.php" class="tab-btn tab-vuln active">🔓 Vulnerable</a>
            <a href="safe.php" class="tab-btn tab-safe">🔒 Safe</a>
        </div>

        <div class="content">
            <h2 class="page-title text-danger">🔓 Vulnerable Features</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <!-- XSS Vulnerable -->
            <div class="vuln-section">
                <h3 class="vuln-title">1. Cross-Site Scripting (XSS) - Vulnerable</h3>
                <div class="card">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Masukkan Pesan (HTML akan dirender langsung):</label>
                            <input type="text" name="message" class="form-control" 
                                   placeholder="Coba: <script>alert('XSS')</script> atau <img src=x onerror=alert('XSS')>">
                        </div>
                        <button type="submit" name="submit_message_vuln" class="btn btn-danger">
                            Submit (Vulnerable)
                        </button>
                    </form>

                    <div class="messages-display">
                        <h4>Pesan Terbaru:</h4>
                        <?php while ($msg = mysqli_fetch_assoc($messages_result)): ?>
                            <div class="message-item">
                                <strong><?php echo $msg['username']; ?>:</strong>
                                <!-- VULNERABLE: Direct output without escaping -->
                                <span><?php echo $msg['message']; ?></span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="hint-box">
                    <strong>💡 Cara Exploit:</strong><br>
                    <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code><br>
                    <code>&lt;img src=x onerror=alert('XSS')&gt;</code>
                </div>
            </div>

            <!-- File Upload Vulnerable -->
            <div class="vuln-section">
                <h3 class="vuln-title">2. File Upload - Vulnerable (No Validation)</h3>
                <div class="card">
                    <?php if ($upload_message): ?>
                        <div class="alert alert-info"><?php echo $upload_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Upload File (Semua tipe file diterima):</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                        <button type="submit" name="upload_vuln" class="btn btn-danger">
                            Upload (Vulnerable)
                        </button>
                    </form>

                    <div class="files-list">
                        <h4>File yang Diupload:</h4>
                        <?php while ($file = mysqli_fetch_assoc($files_result)): ?>
                            <div class="file-item">
                                📁 <?php echo htmlspecialchars($file['filename']); ?> 
                                (<?php echo round($file['filesize']/1024, 2); ?> KB)
                                - <?php echo htmlspecialchars($file['filetype']); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="hint-box">
                    <strong>💡 Kerentanan:</strong><br>
                    • Tidak ada validasi tipe file<br>
                    • Tidak ada validasi ukuran file<br>
                    • Bisa upload .php, .exe, .sh, dll<br>
                    • Berpotensi RCE (Remote Code Execution)
                </div>
            </div>

            <!-- Broken Access Control -->
            <div class="vuln-section">
                <h3 class="vuln-title">3. Broken Access Control - Vulnerable</h3>
                <div class="card card-danger">
                    <h4>⚠️ Panel Admin (Semua User Bisa Akses!)</h4>
                    <p class="text-warning">Halaman ini seharusnya hanya bisa diakses oleh admin, tapi tidak ada pengecekan role!</p>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($all_users_result)): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="hint-box">
                    <strong>💡 Kerentanan:</strong><br>
                    • Tidak ada pengecekan role user<br>
                    • Semua user bisa akses data sensitif<br>
                    • Tidak ada authorization control
                </div>
            </div>
        </div>
    </div>
</body>
</html>