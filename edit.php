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

// Ambil data task yang akan diedit (hanya milik user yang login)
$stmt = $conn->prepare("SELECT * FROM todos WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
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
    
    // Validasi tanggal - tidak boleh tanggal yang sudah lewat (kecuali task sudah completed)
    if (!empty($due_date) && !$task['is_completed']) {
        $currentDate = new DateTime();
        $selectedDate = new DateTime($due_date);
        
        // Set waktu ke awal hari untuk perbandingan yang akurat
        $currentDate->setTime(0, 0, 0);
        $selectedDate->setTime(0, 0, 0);
        
        if ($selectedDate < $currentDate) {
            $errors[] = "Due date cannot be in the past. Please select today or a future date.";
        }
    }
    
    if (empty($errors)) {
        // Update data di database (hanya milik user yang login)
        $stmt = $conn->prepare("UPDATE todos SET title = ?, description = ?, due_date = ?, priority = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssii", $title, $description, $due_date, $priority, $id, $user_id);
        
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
        
        <form action="edit.php?id=<?php echo $id; ?>" method="POST" id="editTaskForm">
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
                <input type="date" id="due_date" name="due_date" class="form-control" 
                       value="<?php echo htmlspecialchars($task['due_date']); ?>"
                       <?php if (!$task['is_completed']): ?>min="<?php echo date('Y-m-d'); ?>"<?php endif; ?>>
                <?php if (!$task['is_completed']): ?>
                    <small class="form-text text-muted">Due date cannot be in the past</small>
                <?php else: ?>
                    <small class="form-text text-muted">Task is completed - date restrictions removed</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority" class="form-control">
                    <option value="low" <?php echo ($task['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo ($task['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo ($task['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Task</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dueDateInput = document.getElementById('due_date');
    const form = document.getElementById('editTaskForm');
    const isCompleted = <?php echo $task['is_completed'] ? 'true' : 'false'; ?>;
    
    // Jika task belum completed, terapkan validasi tanggal
    if (!isCompleted) {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dueDateInput.setAttribute('min', today);
        
        // Validasi saat form disubmit
        form.addEventListener('submit', function(e) {
            const selectedDate = dueDateInput.value;
            
            if (selectedDate) {
                const selected = new Date(selectedDate);
                const current = new Date();
                
                // Set waktu ke awal hari untuk perbandingan
                current.setHours(0, 0, 0, 0);
                selected.setHours(0, 0, 0, 0);
                
                if (selected < current) {
                    e.preventDefault();
                    alert('Due date cannot be in the past. Please select today or a future date.');
                    dueDateInput.focus();
                    return false;
                }
            }
        });
        
        // Validasi real-time saat user mengubah tanggal
        dueDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            
            if (selectedDate) {
                                const selected = new Date(selectedDate);
                const current = new Date();
                
                // Set waktu ke awal hari untuk perbandingan
                current.setHours(0, 0, 0, 0);
                selected.setHours(0, 0, 0, 0);
                
                if (selected < current) {
                    this.style.borderColor = '#dc3545';
                    this.style.backgroundColor = '#f8d7da';
                    
                    // Tampilkan pesan error
                    let errorMsg = document.getElementById('date-error');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.id = 'date-error';
                        errorMsg.className = 'text-danger mt-1';
                        this.parentNode.appendChild(errorMsg);
                    }
                    errorMsg.textContent = 'Due date cannot be in the past';
                } else {
                    this.style.borderColor = '';
                    this.style.backgroundColor = '';
                    
                    // Hapus pesan error jika ada
                    const errorMsg = document.getElementById('date-error');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            }
        });
    }
});
</script>

<style>
.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

.text-danger {
    color: #dc3545 !important;
}

.mt-1 {
    margin-top: 0.25rem;
}

.alert {
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}
</style>

<?php
include 'includes/footer.php';
$conn->close();
?>

