<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $todo_id = intval($_POST['todo_id']);
    $status = intval($_POST['status']); // 0 = belum selesai, 1 = selesai
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE todos SET is_completed = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $status, $todo_id, $user_id);
    $stmt->execute();
}

header("Location: index.php");
exit;
