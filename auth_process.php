<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=todo_app;charset=utf8mb4", "root", "");

if ($_POST['action'] === 'register') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $password]);

    // Setelah berhasil menyimpan user
    echo "<script>
    alert('Akun berhasil dibuat!');
    window.location.href = 'login.php';
    </script>";


    exit;
}

if ($_POST['action'] === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header("Location: index.php");
        exit;
    } else {
       echo "<script>alert('Email atau password salah.'); window.location.href='login.php';</script>";

    }
}
