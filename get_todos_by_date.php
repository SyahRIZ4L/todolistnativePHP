<?php
header('Content-Type: application/json');

if (!isset($_GET['date'])) {
    echo json_encode([]);
    exit;
}

$date = $_GET['date'];

$pdo = new PDO("mysql:host=localhost;dbname=todo_app;charset=utf8mb4", "root", "");
$stmt = $pdo->prepare("SELECT id, title, description, priority FROM todos WHERE due_date = ?");
$stmt->execute([$date]);
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($todos);
