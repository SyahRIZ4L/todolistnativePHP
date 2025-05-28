<?php
session_start();
require_once 'config/database.php';

if ($_POST['action'] === 'register') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Semua field harus diisi!'
        ];
        header('Location: register.php');
        exit;
    }
    
    // Validasi konfirmasi password
    if ($password !== $confirm_password) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Password dan konfirmasi password tidak cocok!'
        ];
        header('Location: register.php');
        exit;
    }
    
    // Validasi panjang password
    if (strlen($password) < 6) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Password minimal 6 karakter!'
        ];
        header('Location: register.php');
        exit;
    }
    
    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Email sudah terdaftar!'
        ];
        header('Location: register.php');
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Registrasi berhasil! Silakan login.'
        ];
        header('Location: login.php');
    } else {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Terjadi kesalahan saat registrasi!'
        ];
        header('Location: register.php');
    }
    exit;
}

// Tambahkan logic untuk LOGIN
if ($_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Email dan password harus diisi!'
        ];
        header('Location: login.php');
        exit;
    }
    
    // Cari user berdasarkan email
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect ke halaman utama
            header('Location: index.php');
            exit;
        } else {
            // Password salah
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Password salah!'
            ];
            header('Location: login.php');
            exit;
        }
    } else {
        // Email tidak ditemukan
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Email tidak terdaftar!'
        ];
        header('Location: login.php');
        exit;
    }
}

// Jika tidak ada action yang cocok, redirect ke login
header('Location: login.php');
exit;
?>
