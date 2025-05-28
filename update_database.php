<?php
require_once 'config/database.php';

// Tambah kolom completed_at
$sql = "ALTER TABLE todos ADD COLUMN completed_at DATETIME NULL AFTER is_completed";

if ($conn->query($sql) === TRUE) {
    echo "Column 'completed_at' added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?>
