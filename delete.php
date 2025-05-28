<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include konfigurasi database
require_once 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if ($id > 0) {
    // Hapus data dari database (hanya milik user yang login)
    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}

// Redirect kembali ke halaman utama
header("Location: index.php");
exit();
?>
