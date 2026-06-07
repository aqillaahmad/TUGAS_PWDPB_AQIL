<?php
session_start();
require_once 'koneksi.php';

$error = '';
$success = '';
$activeTab = isset($_POST['register']) ? 'register' : 'login';

// Proses Login
if (isset($_POST['login'])) {
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE username='{$_POST['username']}'"));
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: tiket.php");
        exit();
    }
    $error = "Username atau password salah!";
}

// Proses Register
if (isset($_POST['register'])) {
    if ($_POST['reg_password'] != $_POST['confirm_password']) {
        $error = "Password tidak cocok!";
    } elseif (strlen($_POST['reg_password']) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif (!mysqli_query($conn, "INSERT INTO users (username, password, email) VALUES ('{$_POST['reg_username']}', '" . password_hash($_POST['reg_password'], PASSWORD_DEFAULT) . "', '{$_POST['email']}')")) {
        $error = "Username sudah digunakan!";
    } else {
        $success = "Registrasi berhasil! Silakan login.";
        $activeTab = 'login';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Tiket Online</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .box { width: 400px; background: #fff; border: 1px solid #ccc; padding: 20px; margin: 20px auto; }
        h3 { text-align: center; margin-bottom: 15px; }
        table { width: 100%; }
        td { padding: 6px; }
        input, select { width: 100%; padding: 6px; box-sizing: border-box; }
        button { padding: 6px 15px; margin: 5px; cursor: pointer; background: #4CAF50; color: white; border: none; }
        .error, .success { padding: 10px; margin-bottom: 15px; border: 1px solid; }
        .error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .success { background: #d4edda; color: #155724; border-color: #c3e6cb; }
        .tab { margin-bottom: 15px; }
        .tab button { background: #f2f2f2; color: #000; border: 1px solid #ccc; border-bottom: none; padding: 8px 15px; margin-right: 5px; }
        .tab button.active { background: #fff; border-bottom: 1px solid #fff; }
        .pane { display: none; }
        .pane.active { display: block; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>

<div class="box">
    <h3>TIKET ONLINE<br>JAKARTA - MALAYSIA</h3>
    
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    
    <div class="tab">
        <button class="<?= $activeTab=='login' ? 'active' : '' ?>" onclick="showTab('login')">LOGIN</button>
        <button class="<?= $activeTab=='register' ? 'active' : '' ?>" onclick="showTab('register')">DAFTAR</button>
    </div>
    
    <div id="login" class="pane <?= $activeTab=='login' ? 'active' : '' ?>">
        <form method="post">
            <table>
                <tr><td width="100">Username</td><td><input type="text" name="username" required></td></tr>
                <tr><td>Password</td><td><input type="password" name="password" required></td></tr>
                <tr><td colspan="2" align="center"><button type="submit" name="login">LOGIN</button></td></tr>
            </table>
        </form>
    </div>
    
    <div id="register" class="pane <?= $activeTab=='register' ? 'active' : '' ?>">
        <form method="post">
            <table>
                <tr><td width="100">Username</td><td><input type="text" name="reg_username" required></td></tr>
                <tr><td>Email</td><td><input type="email" name="email"></td></tr>
                <tr><td>Password</td><td><input type="password" name="reg_password" required></td></tr>
                <tr><td>Konfirmasi</td><td><input type="password" name="confirm_password" required></td></tr>
                <tr><td colspan="2" align="center"><button type="submit" name="register">DAFTAR</button></td></tr>
            </table>
        </form>
    </div>
</div>

<div class="footer">&copy; 2024 Tiket Online Jakarta - Malaysia</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab button').forEach(b => b.classList.remove('active'));
    document.getElementById(tab).classList.add('active');
    event.target.classList.add('active');
}
</script>
</body>
</html>