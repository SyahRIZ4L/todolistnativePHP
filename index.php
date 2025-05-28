<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}




// Include konfigurasi database
require_once 'config/database.php';



// Query untuk mendapatkan semua todo
$query = "SELECT * FROM todos ORDER BY 
          CASE priority 
              WHEN 'high' THEN 1 
              WHEN 'medium' THEN 2 
              WHEN 'low' THEN 3 
          END, due_date ASC";
$result = $conn->query($query);

// Include header
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script src="https://cdn.tailwindcss.com"></script>

<nav class="bg-white shadow-lg p-4 mb-6 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">📋 To-Do List</h1>
    
    <div class="relative">
        <button id="user-menu-button" class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-all duration-200">
            <span class="font-medium text-gray-700"><?= htmlspecialchars($_SESSION['name']) ?></span>
            <svg class="w-4 h-4 text-gray-600 transition-transform duration-200" id="dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
            <a href="profil.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Profile</a>
            <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 border-t border-gray-100">Logout</a>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const priorityFilter = document.getElementById('priority-filter');
    const allTasks = document.querySelectorAll('.task-card');
    
    let currentStatusFilter = 'all';
    let currentPriorityFilter = 'all';

    // Function untuk apply filter
    function applyFilters() {
        allTasks.forEach(task => {
            const isCompleted = task.classList.contains('completed');
            const taskPriority = task.getAttribute('data-priority');
            
            let showTask = true;
            
            // Filter berdasarkan status
            if (currentStatusFilter === 'completed' && !isCompleted) {
                showTask = false;
            } else if (currentStatusFilter === 'pending' && isCompleted) {
                showTask = false;
            }
            
            // Filter berdasarkan priority
            if (currentPriorityFilter !== 'all' && taskPriority !== currentPriorityFilter) {
                showTask = false;
            }
            
            task.style.display = showTask ? 'block' : 'none';
        });
    }

    // Event listener untuk filter status
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Hilangkan active dari semua tombol
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.style.backgroundColor = '';
                btn.style.color = '';
            });

            // Aktifkan tombol yang diklik
            button.classList.add('active');
            button.style.backgroundColor = '#3182ce';
            button.style.color = 'white';

            currentStatusFilter = button.getAttribute('data-filter');
            applyFilters();
        });
    });

    // Event listener untuk filter priority
    priorityFilter.addEventListener('change', function() {
        currentPriorityFilter = this.value;
        applyFilters();
    });
});
</script>


<style>
.rotate-180 {
    transform: rotate(180deg);
}

#user-dropdown {
    transition: opacity 0.2s ease, transform 0.2s ease;
    transform-origin: top right;
}

#user-dropdown:not(.hidden) {
    opacity: 1;
    transform: scale(1);
    display: block;
}
</style>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>

    


<main class="main-content">
    <div class="add-task">
        <div class="action-buttons" style="display: flex; gap: 15px; margin-bottom: 20px;">
    <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Task</a>
    <a href="calendar.php" class="btn btn-secondary"><i class="fas fa-calendar-alt"></i> View Calendar</a>
</div>
    </div>

    <div class="task-filters" style="max-width: 700px; margin: 0 auto 20px auto; display: flex; gap: 10px; justify-content: center; align-items: center;">
    <button class="filter-btn active" data-filter="all" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #ccc; cursor: pointer; background-color: #3182ce; color: white;">All Tasks</button>
    <button class="filter-btn" data-filter="completed" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #ccc; cursor: pointer; background-color: white;">Completed</button>
    <button class="filter-btn" data-filter="pending" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #ccc; cursor: pointer; background-color: white;">Pending</button>
    
    <!-- Dropdown Priority Filter -->
    <select id="priority-filter" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #ccc; cursor: pointer; background-color: white;">
        <option value="all">All Priorities</option>
        <option value="high">High Priority</option>
        <option value="medium">Medium Priority</option>
        <option value="low">Low Priority</option>
    </select>
</div>

    <div class="task-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="task-card <?php echo $row['is_completed'] ? 'completed' : ''; ?>" data-priority="<?php echo $row['priority']; ?>">
                    <div class="task-header">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <span class="priority-badge <?php echo $row['priority']; ?>">
                            <?php echo ucfirst($row['priority']); ?>
                        </span>
                    </div>
                    <div class="task-body">
                        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        <?php if ($row['due_date']): ?>
                            <div class="task-due">
                                <i class="fas fa-calendar-alt"></i>
                                Due: <?php echo date('M d, Y', strtotime($row['due_date'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="task-actions">
                        <a href="complete.php?id=<?php echo $row['id']; ?>" 
                            class="btn btn-action complete-btn <?php echo $row['is_completed'] ? 'bg-yellow-500 text-white' : 'bg-cyan-300 text-gray-700'; ?>">
                        <i class="fas fa-check"></i> <?php echo $row['is_completed'] ? 'Undo' : 'Complete'; ?>
                    </a>

                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-action edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-action delete-btn" onclick="return confirm('Are you sure you want to delete this task?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>No tasks found</h3>
                <p>Add your first task by clicking the "Add New Task" button</p>
            </div>
        <?php endif; ?>
    </div>
</main>
<script src="assets/js/script.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const allTasks = document.querySelectorAll('.todo-list ul li');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Hilangkan active dari semua tombol
            filterButtons.forEach(btn => btn.classList.remove('active'));

            // Aktifkan tombol yang diklik
            button.classList.add('active');

            const filter = button.getAttribute('data-filter');

            allTasks.forEach(task => {
                if (filter === 'all') {
                    task.style.display = 'block';
                } else if (filter === 'completed' && task.classList.contains('completed-task')) {
                    task.style.display = 'block';
                } else if (filter === 'pending' && task.classList.contains('pending-task')) {
                    task.style.display = 'block';
                } else {
                    task.style.display = 'none';
                }
            });
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('user-menu-button');
    const menu = document.getElementById('user-dropdown');
    const arrow = document.getElementById('dropdown-arrow');

    button.addEventListener('click', function(e) {
        e.stopPropagation(); // Mencegah penutupan langsung saat klik
        menu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    });

    document.addEventListener('click', function() {
        menu.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    });
});
</script>

<script src="assets/js/script.js"></script>



</body>
</html>

<?php
// Include footer
include 'includes/footer.php';

// Tutup koneksi
$conn->close();
?>