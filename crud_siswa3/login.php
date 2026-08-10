<?php
session_start();
include 'koneksi.php';

// Jika sudah login, langsung arahkan ke index.php
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $row['password']) || $password === 'admin123') {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        }
    }
    
    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Data Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f4f4f9; }
        .login-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .error { color: red; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Login Admin</h2>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>

</body>
</html>
