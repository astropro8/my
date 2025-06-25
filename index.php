<?php
// Определяем текущую страницу для подсветки меню
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Портал «Мой Не Сам» — Главная</title>
  <style>
    body {
      background-color: #121212;
      color: #e0e0e0;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }
    nav {
      background-color: #1f1f1f;
      padding: 15px 30px;
      display: flex;
      gap: 20px;
      justify-content: center;
      border-radius: 8px;
      margin-bottom: 40px;
      box-shadow: 0 0 10px #1976d2;
    }
    nav a {
      color: #90caf9;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1em;
      padding: 8px 16px;
      border-radius: 6px;
      transition: background-color 0.3s;
    }
    nav a:hover {
      background-color: #005f86;
      color: #fff;
    }
    nav a.active {
      background-color: #1976d2;
      color: #fff;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
      background-color: #1e1e1e;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px #1976d2;
      text-align: center;
    }
    h1 {
      color: #90caf9;
      margin-bottom: 20px;
    }
    p {
      font-size: 1.1em;
      line-height: 1.5;
      color: #ccc;
    }
  </style>
</head>
<body>
  <nav>
    <a href="register.php" class="<?= $currentPage === 'register.php' ? 'active' : '' ?>">Регистрация</a>
    <a href="login.php" class="<?= $currentPage === 'login.php' ? 'active' : '' ?>">Вход</a>
    <a href="requests.php" class="<?= $currentPage === 'requests.php' ? 'active' : '' ?>">Мои заявки</a>
    <a href="admin.php" class="<?= $currentPage === 'admin.php' ? 'active' : '' ?>">Админ-панель</a>
  </nav>

  <div class="container">
    <h1>Добро пожаловать на портал клининговых услуг «Мой Не Сам»</h1>
    <p>Используйте меню выше для перехода к регистрации, входу, просмотру и созданию заявок, а также для доступа к админ-панели.</p>
  </div>
</body>
</html>
