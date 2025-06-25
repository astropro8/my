<?php
$currentPage = 'register';
require_once 'functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$fullname || !$phone || !$email || !$login || !$password) {
        $errors[] = 'Все поля обязательны для заполнения.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
    } else {
        if (registerUser($fullname, $phone, $email, $login, $password)) {
            $success = true;
        } else {
            $errors[] = 'Пользователь с таким логином или email уже существует.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Регистрация - Мой Не Сам</title>
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
    <h1>📝 Регистрация</h1>

    <?php if ($success): ?>
      <p style="color: #4caf50;">Регистрация прошла успешно! Теперь вы можете <a href="login.php" style="color:#90caf9;">войти</a>.</p>
    <?php endif; ?>

    <?php if ($errors): ?>
      <ul style="color: #f44336;">
        <?php foreach ($errors as $error): ?>
          <li><?=htmlspecialchars($error)?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" action="">
      <label for="fullname">ФИО</label>
      <input type="text" id="fullname" name="fullname" value="<?=htmlspecialchars($_POST['fullname'] ?? '')?>" required />

      <label for="phone">Телефон</label>
      <input type="tel" id="phone" name="phone" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>" placeholder="+7 900 123-45-67" pattern="^\+7\s?\d{3}\s?\d{3}[- ]?\d{2}[- ]?\d{2}$" required />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required />

      <label for="login">Логин</label>
      <input type="text" id="login" name="login" value="<?=htmlspecialchars($_POST['login'] ?? '')?>" required />

      <label for="password">Пароль</label>
      <input type="password" id="password" name="password" minlength="6" required />

      <button type="submit">Зарегистрироваться</button>
    </form>
  </div>
</body>
</html>
