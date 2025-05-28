<?php
header('Content-Type: application/json');

$pdo = new PDO("mysql:host=localhost;dbname=todo_app;charset=utf8mb4", "root", "");

// Ambil data to-do, mapping due_date ke start (FullCalendar butuh 'start' key)
$stmt = $pdo->query("SELECT id, title, due_date AS start, description, priority FROM todos");
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($todos);
