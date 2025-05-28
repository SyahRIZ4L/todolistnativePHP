document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk filter tasks
    const filterTasks = (filterType) => {
        const tasks = document.querySelectorAll('.task-card');
        
        tasks.forEach(task => {
            const isCompleted = task.classList.contains('completed');
            
            switch(filterType) {
                case 'all':
                    task.style.display = 'block';
                    break;
                case 'completed':
                    task.style.display = isCompleted ? 'block' : 'none';
                    break;
                case 'pending':
                    task.style.display = !isCompleted ? 'block' : 'none';
                    break;
            }
        });
    };

    // Event listener untuk tombol filter
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class dari semua tombol
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            // Tambahkan active class ke tombol yang diklik
            this.classList.add('active');
            
            // Filter tasks
            const filterType = this.getAttribute('data-filter');
            filterTasks(filterType);
        });
    });

    // Toggle dropdown user
    const userMenuButton = document.getElementById('user-menu-button');
    if (userMenuButton) {
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('user-dropdown');
            dropdown.classList.toggle('hidden');
        });
    }

    // Close dropdown ketika klik di luar
    document.addEventListener('click', function() {
        const dropdown = document.getElementById('user-dropdown');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });

    // Animasi untuk task card
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `all 0.3s ease ${index * 0.1}s`;
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 50);
    });
});
// Mark task as complete
function setupCompleteButtons() {
    document.querySelectorAll('.complete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const taskId = this.getAttribute('data-task-id');
            
            // Kirim request AJAX
            fetch(`complete.php?id=${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const taskCard = this.closest('.task-card');
                        taskCard.classList.toggle('completed');
                        this.innerHTML = taskCard.classList.contains('completed') 
                            ? '<i class="fas fa-check"></i> Undo' 
                            : '<i class="fas fa-check"></i> Complete';
                    }
                });
        });
    });
}
// Add new task without page reload
const addTaskForm = document.getElementById('add-task-form');
if (addTaskForm) {
    addTaskForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('add_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tambahkan task baru ke DOM
                const taskList = document.querySelector('.task-list');
                const newTask = createTaskElement(data.task);
                taskList.prepend(newTask);
                
                // Reset form
                this.reset();
            }
        });
    });
}

function createTaskElement(task) {
    const taskCard = document.createElement('div');
    taskCard.className = `task-card ${task.is_completed ? 'completed' : ''}`;
    taskCard.innerHTML = `
        <div class="task-header">
            <h3>${task.title}</h3>
            <span class="priority-badge ${task.priority}">
                ${task.priority}
            </span>
        </div>
        <div class="task-body">
            <p>${task.description}</p>
            ${task.due_date ? `
            <div class="task-due">
                <i class="fas fa-calendar-alt"></i>
                Due: ${new Date(task.due_date).toLocaleDateString()}
            </div>` : ''}
        </div>
        <div class="task-actions">
            <a href="complete.php?id=${task.id}" class="btn btn-action complete-btn">
                <i class="fas fa-check"></i> ${task.is_completed ? 'Undo' : 'Complete'}
            </a>
            <a href="edit.php?id=${task.id}" class="btn btn-action edit-btn">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="delete.php?id=${task.id}" class="btn btn-action delete-btn" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>
    `;
    
    return taskCard;
}
document.addEventListener('DOMContentLoaded', function() {
    const profilePictureInput = document.getElementById('profile_picture');
    const profilePicture = document.querySelector('.profile-picture img');
    
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    profilePicture.src = event.target.result;
                    // Auto submit form when image is selected
                    document.querySelector('.upload-form').submit();
                }
                
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }
    
    // Auto submit form when bio/name is changed
    const profileForm = document.querySelector('.profile-form');
    if (profileForm) {
        profileForm.addEventListener('change', function() {
            this.submit();
        });
    }
});