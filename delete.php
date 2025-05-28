<?php
// Include konfigurasi database
require_once 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Redirect kembali ke halaman utama
header("Location: index.php");
exit();
?>