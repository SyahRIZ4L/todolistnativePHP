<?php
// Include konfigurasi database
require_once 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data task yang akan diedit
$stmt = $conn->prepare("SELECT * FROM todos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    header("Location: index.php");
    exit();
}

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    
    // Validasi input
    $errors = [];
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    if (empty($errors)) {
        // Update data di database
        $stmt = $conn->prepare("UPDATE todos SET title = ?, description = ?, due_date = ?, priority = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $description, $due_date, $priority, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit();
        } else {
            $errors[] = "Failed to update task: " . $conn->error;
        }
    }
}

include 'includes/header.php';
?>

<main class="main-content">
    <div class="form-container">
        <h2><i class="fas fa-edit"></i> Edit Task</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="edit.php?id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label for="title">Title*</label>
                <input type="text" id="title" name="title" required class="form-control" value="<?php echo htmlspecialchars($task['title']); ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($task['due_date']); ?>">
            </div>
            
            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority" class="form-control">
                    <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Task</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php
include 'includes/footer.php';
$conn->close();
?>