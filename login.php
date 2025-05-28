<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <form action="auth_process.php" method="POST" class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <?php if (isset($_SESSION['flash'])): ?>
    <div class="mb-4 p-3 rounded text-white <?= $_SESSION['flash']['type'] === 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
    <input type="hidden" name="action" value="login">
    <input type="email" name="email" placeholder="Email" required class="w-full mb-4 p-2 border rounded">
    <input type="password" name="password" placeholder="Password" required class="w-full mb-4 p-2 border rounded">
    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">Login</button>
    <p class="text-center mt-4 text-sm">Belum punya akun? <a href="register.php" class="text-blue-600">Daftar</a></p>
  </form>
</body>
</html>
