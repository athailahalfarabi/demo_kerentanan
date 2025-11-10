<?php
require_once 'config.php';

$error = '';
$success = '';

// Handle Login (Vulnerable - SQL Injection)
if (isset($_POST['login_vuln'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // VULNERABLE: Direct string concatenation
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    
    // Log query untuk demo
    error_log("Vulnerable Query: " . $query);
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Login gagal! Username atau password salah.";
    }
}

// Handle Login (Safe - Prepared Statement)
if (isset($_POST['login_safe'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // SAFE: Using prepared statement
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Login gagal! Username atau password salah.";
    }
    mysqli_stmt_close($stmt);
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
    <title>Login - Demo Keamanan Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="icon-lock">🔒</div>
                <h1>Login</h1>
                <p>Demo Keamanan Data</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Registrasi berhasil! Silakan login.</div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required class="form-control">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required class="form-control">
                </div>
                
                <div class="button-group">
                    <button type="submit" name="login_vuln" class="btn btn-danger">
                        Login (Vulnerable)
                    </button>
                    <button type="submit" name="login_safe" class="btn btn-success">
                        Login (Safe)
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="register.php" class="link">Belum punya akun? Register</a>
            </div>

            <div class="hint-box">
                <strong>💡 Hint SQL Injection:</strong><br>
                <code>Username: ' OR '1'='1</code><br>
                <code>Password: ' OR '1'='1</code><br>
                <small>Coba gunakan input di atas pada tombol "Login (Vulnerable)"</small>
            </div>
        </div>
    </div>
</body>
</html>