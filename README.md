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
