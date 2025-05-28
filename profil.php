<?php
require 'config/database.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$user_stmt = $conn->prepare("SELECT name, bio, profile_picture FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Update profil jika ada form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $bio = $conn->real_escape_string($_POST['bio']);

    $profile_pic = $user['profile_picture'];
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "assets/uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
        $profile_pic = basename($_FILES['profile_picture']['name']);
    }

    $update_stmt = $conn->prepare("UPDATE users SET name = ?, bio = ?, profile_picture = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $name, $bio, $profile_pic, $user_id);
    $update_stmt->execute();

    header("Location: profile.php");
    exit;
}

// Hitung statistik todo - PERBAIKAN DI SINI
$stats = [
    'total' => 0,
    'completed' => 0,
    'pending' => 0
];

$stats_stmt = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(is_completed = 1) as completed,
    SUM(is_completed = 0) as pending
    FROM todos WHERE user_id = ?");
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();

if ($stats_result && $stats_row = $stats_result->fetch_assoc()) {
    $stats = $stats_row;
}

include 'includes/header.php';
?>

<!-- TAMPILAN PROFIL -->
<div class="profile-container" style="text-align: center; margin-bottom: 30px;">
    <h2>Hallo Kamu Login Sebagai</h2>
    <h1><?= htmlspecialchars($user['name']) ?></h1>
</div>

<!-- STATISTIK TODO - PERBAIKAN TAMPILAN -->
<div class="profile-stats" style="display: flex; justify-content: center; gap: 30px; margin-bottom: 40px;">
    <div class="stat-card" style="border: 1px solid #ccc; padding: 20px; border-radius: 5px;">
        <h3>Total Tasks</h3>
        <p style="font-size: 24px; font-weight: bold;"><?= $stats['total'] ?></p>
    </div>
    <div class="stat-card completed" style="border: 1px solid #4CAF50; padding: 20px; border-radius: 5px; background-color: #f8f8f8;">
        <h3>Completed</h3>
        <p style="font-size: 24px; font-weight: bold; color: #4CAF50;"><?= $stats['completed'] ?></p>
    </div>
    <div class="stat-card pending" style="border: 1px solid #f44336; padding: 20px; border-radius: 5px; background-color: #f8f8f8;">
        <h3>Pending</h3>
        <p style="font-size: 24px; font-weight: bold; color: #f44336;"><?= $stats['pending'] ?></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>