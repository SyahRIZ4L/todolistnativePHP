<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include konfigurasi database
require_once 'config/database.php';

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id']; // Ambil user_id dari session
    
    // Validasi input
    $errors = [];
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    // Validasi tanggal - tidak boleh tanggal yang sudah lewat
    if (!empty($due_date)) {
        $currentDate = date('Y-m-d'); // Tanggal hari ini
        
        if ($due_date < $currentDate) {
            $errors[] = "Due date cannot be in the past. Please select today or a future date.";
        }
    }
    
    if (empty($errors)) {
        // Insert data ke database dengan user_id dari session
        $stmt = $conn->prepare("INSERT INTO todos (title, description, due_date, priority, is_completed, user_id) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("ssssi", $title, $description, $due_date, $priority, $user_id);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit();
        } else {
            $errors[] = "Failed to add task: " . $conn->error;
        }
    }
}

include 'includes/header.php';
?>

<main class="main-content">
    <div class="form-container">
        <h2><i class="fas fa-plus-circle"></i> Add New Task</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="add.php" method="POST" id="addTaskForm">
            <div class="form-group">
                <label for="title">Title*</label>
                <input type="text" id="title" name="title" required class="form-control" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" class="form-control" value="<?php echo isset($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : ''; ?>" min="<?php echo date('Y-m-d'); ?>">
                <small class="form-text text-muted">Due date cannot be in the past</small>
                <div id="date-error" class="text-danger mt-1" style="display: none;"></div>
            </div>
            
            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority" class="form-control">
                    <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" id="submitBtn" class="btn btn-primary"><i class="fas fa-save"></i> Save Task</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dueDateInput = document.getElementById('due_date');
    const form = document.getElementById('addTaskForm');
    const submitBtn = document.getElementById('submitBtn');
    const dateError = document.getElementById('date-error');
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    dueDateInput.setAttribute('min', today);
    
    function validateDate() {
        const selectedDate = dueDateInput.value;
        
        if (selectedDate) {
            const today = new Date().toISOString().split('T')[0];
            
            if (selectedDate < today) {
                // Tanggal invalid
                dueDateInput.style.borderColor = '#dc3545';
                dueDateInput.style.backgroundColor = '#f8d7da';
                dateError.textContent = 'Due date cannot be in the past';
                dateError.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';
                return false;
            } else {
                // Tanggal valid
                dueDateInput.style.borderColor = '#28a745';
                dueDateInput.style.backgroundColor = '#d4edda';
                dateError.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                return true;
            }
        } else {
            // Tanggal kosong (diizinkan)
            dueDateInput.style.borderColor = '';
            dueDateInput.style.backgroundColor = '';
            dateError.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            return true;
        }
    }
    
    // Validasi saat input berubah
    dueDateInput.addEventListener('change', validateDate);
    dueDateInput.addEventListener('input', validateDate);
    
    // Validasi saat form disubmit
    form.addEventListener('submit', function(e) {
        if (!validateDate()) {
            e.preventDefault();
            alert('Please select a valid due date (today or future date).');
            dueDateInput.focus();
            return false;
        }
    });
    
    // Cek validasi awal jika ada value
    if (dueDateInput.value) {
        validateDate();
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
    font-size: 0.875rem;
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

.form-group {
    margin-bottom: 1rem;
}

.form-control {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ddd;
    border-radius: 0.25rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 1rem;
}
</style>

<?php
include 'includes/footer.php';
$conn->close();
?>
