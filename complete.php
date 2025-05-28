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
    // Ambil status saat ini
    $stmt = $conn->prepare("SELECT is_completed FROM todos WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
        $new_status = $task['is_completed'] ? 0 : 1; // Toggle status
        
        // Update status dan completed_at
        if ($new_status == 1) {
            // Task sedang di-complete, set completed_at ke waktu sekarang
            $completed_at = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("UPDATE todos SET is_completed = ?, completed_at = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("isii", $new_status, $completed_at, $id, $user_id);
        } else {
            // Task sedang di-undo, hapus completed_at
            $stmt = $conn->prepare("UPDATE todos SET is_completed = ?, completed_at = NULL WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $new_status, $id, $user_id);
        }
        
        $stmt->execute();
    }
}

// Redirect kembali ke halaman utama
header("Location: index.php");
exit();
?>
