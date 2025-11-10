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

// SAFE: XSS - With sanitization
if (isset($_POST['submit_message_safe'])) {
    $msg = $_POST['message'];
    
    // SAFE: Escape HTML entities
    $msg_safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    
    // SAFE: Using prepared statement
    $stmt = mysqli_prepare($conn, "INSERT INTO messages (user_id, message) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $msg_safe);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    $message = "Pesan berhasil disimpan dengan aman!";
}

// SAFE: File Upload - With validation
if (isset($_POST['upload_safe'])) {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $filename = $file['name'];
        $tmp_name = $file['tmp_name'];
        $filesize = $file['size'];
        $filetype = $file['type'];
        
        // SAFE: File type validation
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Get file extension
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        
        if (!in_array($filetype, $allowed_types) || !in_array($file_ext, $allowed_ext)) {
            $upload_message = "Error: Tipe file tidak diizinkan! Hanya JPG, PNG, GIF, dan PDF.";
        } elseif ($filesize > $max_size) {
            $upload_message = "Error: Ukuran file terlalu besar! Maksimal 5MB.";
        } else {
            // SAFE: Rename file with random name
            $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
            $upload_path = "uploads/" . $new_filename;
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                // SAFE: Using prepared statement
                $stmt = mysqli_prepare($conn, "INSERT INTO uploaded_files (user_id, filename, filepath, filesize, filetype) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "issis", $user_id, $filename, $upload_path, $filesize, $filetype);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                $upload_message = "File '$filename' berhasil diupload dengan aman!";
            } else {
                $upload_message = "Upload gagal!";
            }
        }
    }
}

// Get messages (XSS safe)
$stmt = mysqli_prepare($conn, "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC LIMIT 10");
mysqli_stmt_execute($stmt);
$messages_result = mysqli_stmt_get_result($stmt);

// Get uploaded files
$stmt2 = mysqli_prepare($conn, "SELECT * FROM uploaded_files WHERE user_id = ? ORDER BY uploaded_at DESC");
mysqli_stmt_bind_param($stmt2, "i", $user_id);
mysqli_stmt_execute($stmt2);
$files_result = mysqli_stmt_get_result($stmt2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe - Demo Keamanan Data</title>
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
            <a href="vulnerable.php" class="tab-btn tab-vuln">🔓 Vulnerable</a>
            <a href="safe.php" class="tab-btn tab-safe active">🔒 Safe</a>
        </div>

        <div class="content">
            <h2 class="page-title text-success">🔒 Safe Features</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <!-- XSS Safe -->
            <div class="safe-section">
                <h3 class="safe-title">1. Cross-Site Scripting (XSS) - Protected</h3>
                <div class="card">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Masukkan Pesan (HTML akan di-escape):</label>
                            <input type="text" name="message" class="form-control" 
                                   placeholder="Coba input HTML apapun, akan di-escape">
                        </div>
                        <button type="submit" name="submit_message_safe" class="btn btn-success">
                            Submit (Safe)
                        </button>
                    </form>

                    <div class="messages-display">
                        <h4>Pesan Terbaru:</h4>
                        <?php while ($msg = mysqli_fetch_assoc($messages_result)): ?>
                            <div class="message-item">
                                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                                <!-- SAFE: Already escaped when stored -->
                                <span><?php echo $msg['message']; ?></span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="info-box">
                    <strong>✅ Protection:</strong><br>
                    • Input di-sanitasi dengan htmlspecialchars()<br>
                    • HTML entities di-escape<br>
                    • Script tidak akan dieksekusi
                </div>
            </div>

            <!-- File Upload Safe -->
            <div class="safe-section">
                <h3 class="safe-title">2. File Upload - Protected (With Validation)</h3>
                <div class="card">
                    <?php if ($upload_message): ?>
                        <div class="alert <?php echo strpos($upload_message, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
                            <?php echo $upload_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Upload File (Hanya JPG, PNG, GIF, PDF - Max 5MB):</label>
                            <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf">
                        </div>
                        <button type="submit" name="upload_safe" class="btn btn-success">
                            Upload (Safe)
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
                <div class="info-box">
                    <strong>✅ Protection:</strong><br>
                    • Validasi tipe file (whitelist)<br>
                    • Validasi ekstensi file<br>
                    • Validasi ukuran file (max 5MB)<br>
                    • File di-rename dengan nama random
                </div>
            </div>

            <!-- Access Control Safe -->
            <div class="safe-section">
                <h3 class="safe-title">3. Access Control - Protected</h3>
                <div class="card card-success">
                    <?php if ($role === 'admin'): ?>
                        <h4>✅ Panel Admin (Access Granted)</h4>
                        <p class="text-success">Anda adalah admin, akses diberikan.</p>
                        
                        <?php
                        // SAFE: Only show for admin
                        $stmt_users = mysqli_prepare($conn, "SELECT id, username, email, role FROM users");
                        mysqli_stmt_execute($stmt_users);
                        $all_users_result = mysqli_stmt_get_result($stmt_users);
                        ?>
                        
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
                    <?php else: ?>
                        <h4>❌ Access Denied</h4>
                        <p class="text-danger">Anda tidak memiliki akses ke panel admin. Hanya admin yang dapat melihat halaman ini.</p>
                        <div class="alert alert-warning">
                            <strong>Info:</strong> Role Anda saat ini adalah "<?php echo htmlspecialchars($role); ?>". 
                            Login sebagai admin untuk melihat panel admin.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="info-box">
                    <strong>✅ Protection:</strong><br>
                    • Pengecekan role user<br>
                    • Authorization control<br>
                    • Akses dibatasi berdasarkan role<br>
                    • Prepared statements untuk query
                </div>
            </div>
        </div>
    </div>
</body>
</html>