<?php
// Include konfigurasi database
require_once 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Dapatkan status saat ini
    $stmt = $conn->prepare("SELECT is_completed FROM todos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    
    if ($task) {
        // Toggle status
        $new_status = $task['is_completed'] ? 0 : 1;
        
        // Update status
        $stmt = $conn->prepare("UPDATE todos SET is_completed = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_status, $id);
        $stmt->execute();
    }
}

// Redirect kembali ke halaman utama
header("Location: index.php");
exit();
?>