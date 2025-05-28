<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Kalender To-Do List</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
        a.back {
            display: inline-block;
            margin-bottom: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 99;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background: white;
            padding: 20px;
            margin: 10% auto;
            max-width: 500px;
            border-radius: 10px;
        }
        .modal-content button {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<a href="index.php" class="back">← Kembali ke Halaman Utama</a>

<div id='calendar'></div>

<!-- Modal untuk menampilkan daftar to-do -->
<div id="todoModal" class="modal">
    <div class="modal-content">
        <h2>Daftar To-Do</h2>
        <ul id="todoList"></ul>
        <button onclick="closeModal()">Tutup</button>
    </div>
</div>

<script>
// Inisialisasi kalender dan event klik tanggal/event
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'get_all_events.php',  // ambil event dari file PHP
        dateClick: function(info) {
            showTodos(info.dateStr);
        },
        eventClick: function(info) {
            showTodos(info.event.startStr);
        }
    });

    calendar.render();
});

// Fungsi untuk mengambil dan menampilkan to-do berdasarkan tanggal
function showTodos(dateStr) {
    fetch('get_todos_by_date.php?date=' + dateStr)
        .then(res => res.json())
        .then(data => {
            let list = document.getElementById('todoList');
            list.innerHTML = '';

            if (data.length === 0) {
                list.innerHTML = '<li>Tidak ada to-do.</li>';
            } else {
                data.forEach(todo => {
                    let li = document.createElement('li');
                    li.innerHTML = `<strong>${todo.title}</strong> (${todo.priority})<br><small>${todo.description}</small>`;
                    list.appendChild(li);
                });
            }

            document.getElementById('todoModal').style.display = 'block';
        })
        .catch(() => {
            alert('Gagal mengambil data to-do.');
        });
}

// Fungsi untuk menutup modal
function closeModal() {
    document.getElementById('todoModal').style.display = 'none';
}
</script>

</body>
</html>
