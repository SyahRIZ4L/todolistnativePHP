<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <form action="auth_process.php" method="POST" class="bg-white p-8 rounded-lg shadow-md w-full max-w-md" id="registerForm">
    <?php if (isset($_SESSION['flash'])): ?>
    <div class="mb-4 p-3 rounded text-white <?= $_SESSION['flash']['type'] === 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    
    <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
    
    <input type="hidden" name="action" value="register">
    
    <input type="text" name="name" placeholder="Nama" required class="w-full mb-4 p-2 border rounded">
    
    <input type="email" name="email" placeholder="Email" required class="w-full mb-4 p-2 border rounded">
    
    <div class="relative mb-4">
      <input type="password" name="password" id="password" placeholder="Password" required class="w-full p-2 border rounded">
    </div>
    
    <div class="relative mb-4">
      <input type="password" name="confirm_password" id="confirm_password" placeholder="Konfirmasi Password" required class="w-full p-2 border rounded">
      <div id="password-match-message" class="text-sm mt-1 hidden"></div>
    </div>
    
    <button type="submit" id="submitBtn" class="w-full bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">Daftar</button>
    
    <p class="text-center mt-4 text-sm">Sudah punya akun? <a href="login.php" class="text-blue-600">Login</a></p>
  </form>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirm_password');
      const message = document.getElementById('password-match-message');
      const submitBtn = document.getElementById('submitBtn');
      const form = document.getElementById('registerForm');

      function checkPasswordMatch() {
        if (confirmPassword.value === '') {
          message.classList.add('hidden');
          confirmPassword.classList.remove('border-red-500', 'border-green-500');
          return;
        }

        if (password.value === confirmPassword.value) {
          message.textContent = '✓ Password cocok';
          message.className = 'text-sm mt-1 text-green-600';
          confirmPassword.classList.remove('border-red-500');
          confirmPassword.classList.add('border-green-500');
        } else {
          message.textContent = '✗ Password tidak cocok';
          message.className = 'text-sm mt-1 text-red-600';
          confirmPassword.classList.remove('border-green-500');
          confirmPassword.classList.add('border-red-500');
        }
      }

      password.addEventListener('input', checkPasswordMatch);
      confirmPassword.addEventListener('input', checkPasswordMatch);

      form.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
          e.preventDefault();
          alert('Password dan konfirmasi password tidak cocok!');
          return false;
        }
      });
    });
  </script>
</body>
</html>
