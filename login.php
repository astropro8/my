<?php
$currentPage = 'login';
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$login || !$password) {
        $errors[] = 'Введите логин и пароль.';
    } else {
        if (loginUser($login, $password)) {
            header('Location: requests.php');
            exit;
        } else {
            $errors[] = 'Неверный логин или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Вход - Мой Не Сам</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <nav>
    <a href="register.php" class="<?= $currentPage === 'register' ? 'active' : '' ?>">Регистрация</a>
    <a href="login.php" class="<?= $currentPage === 'login' ? 'active' : '' ?>">Вход</a>
    <a href="requests.php" class="<?= $currentPage === 'requests' ? 'active' : '' ?>">Мои заявки</a>
    <a href="admin.php" class="<?= $currentPage === 'admin' ? 'active' : '' ?>">Админ-панель</a>
  </nav>

  <div class="container">
    <h1>🔑 Вход</h1>

    <?php if ($errors): ?>
      <ul style="color: #f44336;">
        <?php foreach ($errors as $error): ?>
          <li><?=htmlspecialchars($error)?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" action="">
      <label for="login">Логин</label>
      <input type="text" id="login" name="login" value="<?=htmlspecialchars($_POST['login'] ?? '')?>" required />

      <label for="password">Пароль</label>
      <input type="password" id="password" name="password" required />

      <button type="submit">Войти</button>
    </form>
  </div>
</body>
</html>
