<?php
require_once 'config.php';

$error = '';
$success = '';

// Handle Registration (Vulnerable)
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // VULNERABLE: No input validation, SQL Injection possible
    $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'user')";
    
    if (mysqli_query($conn, $query)) {
        header('Location: login.php?registered=1');
        exit();
    } else {
        $error = "Registrasi gagal! " . mysqli_error($conn);
    }
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Demo Keamanan Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="icon-lock">👤</div>
                <h1>Register</h1>
                <p>Buat Akun Baru</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required class="form-control">
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required class="form-control">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required class="form-control">
                </div>
                
                <button type="submit" name="register" class="btn btn-primary btn-block">
                    Register
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php" class="link">Sudah punya akun? Login</a>
            </div>

            <div class="hint-box">
                <strong>⚠️ Perhatian:</strong><br>
                <small>Form ini vulnerable terhadap SQL Injection. Password disimpan dalam plain text (tidak di-hash).</small>
            </div>
        </div>
    </div>
</body>
</html>