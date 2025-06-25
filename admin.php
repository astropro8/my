<?php
session_start();
require_once 'config.php';

// Конфигурация администратора
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD_HASH', '$2y$10$1pM7AoSEQxWghlNKQwNbvuhsxYRZggs.UDenEUYCBatKt/J5emm.i');


// Выход из админки
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Авторизация
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        if ($login === ADMIN_LOGIN && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $_SESSION['is_admin'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль.';
        }
    }
} else {
    // Обработка действий с заявками
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
        $request_id = (int)$_POST['request_id'];
        $action = $_POST['action'];
        $allowed_actions = ['accept', 'done', 'reject'];
        if (in_array($action, $allowed_actions)) {
            if ($action === 'accept') {
                $status = 'услуга оказана';
                $rejection_reason = null;
            } elseif ($action === 'done') {
                $status = 'услуга оказана';
                $rejection_reason = null;
            } elseif ($action === 'reject') {
                $status = 'услуга отменена';
                $rejection_reason = trim($_POST['rejection_reason'] ?? '');
            }
            $stmt = $pdo->prepare("UPDATE requests SET status = ?, rejection_reason = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $rejection_reason, $request_id]);
            header('Location: admin.php');
            exit;
        }
    }

    // Получаем заявки с данными пользователей и услуг
    $stmt = $pdo->query("
        SELECT r.id, u.fullname, u.phone, u.email, r.address, s.name AS service, r.service_date, r.payment_type, r.status, r.rejection_reason
        FROM requests r
        JOIN users u ON r.user_id = u.id
        JOIN services s ON r.service_id = s.id
        ORDER BY r.created_at DESC
    ");
    $requests = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Админ-панель - Мой Не Сам</title>
  <style>
    body {
      background-color: #121212;
      color: #e0e0e0;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 1000px;
      margin: 0 auto;
      background-color: #1e1e1e;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px #1976d2;
    }
    h1 {
      color: #90caf9;
      margin-bottom: 20px;
      text-align: center;
    }
    form.login-form {
      max-width: 400px;
      margin: 40px auto;
      background-color: #222;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px #1976d2;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #90caf9;
    }
    input, textarea {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: none;
      font-size: 1em;
      background-color: #2c2c2c;
      color: #e0e0e0;
      box-sizing: border-box;
    }
    textarea {
      resize: vertical;
      min-height: 60px;
    }
    input::placeholder, textarea::placeholder {
      color: #888;
    }
    button {
      background-color: #1976d2;
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 1.1em;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      font-weight: 700;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: #1565c0;
    }
    .error {
      color: #f44336;
      margin-bottom: 20px;
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      box-shadow: 0 2px 12px rgba(25, 118, 210, 0.3);
      font-size: 0.95em;
    }
    th, td {
      border: 1px solid #333;
      padding: 12px 15px;
      text-align: left;
      vertical-align: middle;
      word-break: break-word;
    }
    th {
      background-color: #1976d2;
      color: white;
      user-select: none;
    }
    tbody tr:nth-child(even) {
      background-color: #222;
    }
    tbody tr:hover {
      background-color: #2a2a2a;
    }
    .actions form {
      display: inline-block;
      margin: 0 2px;
    }
    .actions button {
      background-color: #1976d2;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 6px 12px;
      cursor: pointer;
      font-size: 0.9em;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    .actions button.accept {
      background-color: #388e3c;
    }
    .actions button.done {
      background-color: #0288d1;
    }
    .actions button.reject {
      background-color: #d32f2f;
    }
    .actions button:hover {
      opacity: 0.85;
    }
    .logout {
      display: block;
      margin: 20px auto 0;
      text-align: center;
    }
    .logout a {
      color: #90caf9;
      text-decoration: none;
      font-weight: 600;
    }
    .logout a:hover {
      text-decoration: underline;
    }
    .reject-reason {
      display: none;
      margin-top: 5px;
    }
  </style>
  <script>
    function toggleRejectReason(id) {
      var form = document.getElementById('reject_form_' + id);
      if (form.style.display === 'block') {
        form.style.display = 'none';
      } else {
        form.style.display = 'block';
      }
    }
  </script>
</head>
<body>
<?php if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true): ?>
  <form method="post" class="login-form" action="">
    <h1>Вход в админ-панель</h1>
    <?php if (!empty($error)): ?>
      <div class="error"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <label for="login">Логин</label>
    <input type="text" id="login" name="login" placeholder="Введите логин" required autofocus />

    <label for="password">Пароль</label>
    <input type="password" id="password" name="password" placeholder="Введите пароль" required />

    <button type="submit">Войти</button>
  </form>
<?php else: ?>
  <div class="container">
    <h1>🛠️ Админ-панель</h1>
    <table>
      <thead>
        <tr>
          <th>ФИО</th>
          <th>Телефон</th>
          <th>Email</th>
          <th>Адрес</th>
          <th>Услуга</th>
          <th>Дата и время</th>
          <th>Оплата</th>
          <th>Статус</th>
          <th>Причина отклонения</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['fullname'])?></td>
            <td><?=htmlspecialchars($r['phone'])?></td>
            <td><?=htmlspecialchars($r['email'])?></td>
            <td><?=htmlspecialchars($r['address'])?></td>
            <td><?=htmlspecialchars($r['service'])?></td>
            <td><?=htmlspecialchars($r['service_date'])?></td>
            <td><?=htmlspecialchars($r['payment_type'])?></td>
            <td><?=htmlspecialchars($r['status'])?></td>
            <td><?=htmlspecialchars($r['rejection_reason'] ?? '')?></td>
            <td class="actions">
              <?php if ($r['status'] === 'новая заявка'): ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="action" value="accept">
                  <button type="submit" class="accept" title="Подтвердить">✅</button>
                </form>
                <form method="post" style="display:inline;" onsubmit="return confirm('Вы уверены, что хотите отметить заявку как выполненную?');">
                  <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="action" value="done">
                  <button type="submit" class="done" title="Выполнено">✔️</button>
                </form>
                <button type="button" class="reject" title="Отклонить" onclick="toggleRejectReason(<?= $r['id'] ?>)">❌</button>
                <form method="post" style="display:none;" id="reject_form_<?= $r['id'] ?>">
                  <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="action" value="reject">
                  <textarea name="rejection_reason" placeholder="Причина отклонения" required></textarea>
                  <button type="submit" class="reject" style="margin-top:5px;">Отправить отклонение</button>
                </form>
              <?php else: ?>
                <em>Действия недоступны</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="logout">
      <a href="admin.php?logout=1">Выйти из админки</a>
    </div>
  </div>
<?php endif; ?>
</body>
</html>
