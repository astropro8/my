<?php
$currentPage = 'requests';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = currentUser();
$requests = getUserRequests($user['id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Мои заявки - Мой Не Сам</title>
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
    <h1>🗂️ История моих заявок</h1>
    <?php if (!$requests): ?>
      <p>У вас пока нет заявок.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Дата и время</th>
            <th>Вид услуги</th>
            <th>Адрес</th>
            <th>Статус</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($requests as $r): ?>
            <tr>
              <td><?=htmlspecialchars($r['service_date'])?></td>
              <td><?=htmlspecialchars($r['service_name'])?></td>
              <td><?=htmlspecialchars($r['address'])?></td>
              <td><?=htmlspecialchars($r['status'])?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <a href="create_request.php"><button>➕ Создать новую заявку</button></a>
  </div>
</body>
</html>
