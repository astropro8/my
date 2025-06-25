<?php
$currentPage = 'requests';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = currentUser();
$errors = [];
$success = false;

// Получаем список услуг из БД
$stmt = $pdo->query("SELECT id, name FROM services ORDER BY id");
$services = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $datetime = $_POST['datetime'] ?? '';
    $service_id = $_POST['service'] ?? '';
    $payment_type = $_POST['payment'] ?? '';

    if (!$address || !$phone || !$datetime || !$service_id || !$payment_type) {
        $errors[] = 'Все поля обязательны для заполнения.';
    } elseif (!in_array($payment_type, ['наличные', 'банковская карта'])) {
        $errors[] = 'Некорректный тип оплаты.';
    } else {
        // Проверка даты (должна быть в будущем)
        if (strtotime($datetime) <= time()) {
            $errors[] = 'Дата и время должны быть в будущем.';
        } else {
            if (addRequest($user['id'], $address, $phone, $service_id, $datetime, $payment_type)) {
                $success = true;
            } else {
                $errors[] = 'Ошибка при добавлении заявки.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Создание заявки - Мой Не Сам</title>
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
    <h1>➕ Создание новой заявки</h1>

    <?php if ($success): ?>
      <p style="color: #4caf50;">Заявка успешно создана! <a href="requests.php" style="color:#90caf9;">Вернуться к списку заявок</a>.</p>
    <?php endif; ?>

    <?php if ($errors): ?>
      <ul style="color: #f44336;">
        <?php foreach ($errors as $error): ?>
          <li><?=htmlspecialchars($error)?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" action="">
      <label for="address">Адрес</label>
      <input type="text" id="address" name="address" value="<?=htmlspecialchars($_POST['address'] ?? '')?>" required />

      <label for="phone">Контактный телефон</label>
      <input type="tel" id="phone" name="phone" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>" placeholder="+7 900 123-45-67" pattern="^\+7\s?\d{3}\s?\d{3}[- ]?\d{2}[- ]?\d{2}$" required />

      <label for="datetime">Желаемая дата и время</label>
      <input type="datetime-local" id="datetime" name="datetime" value="<?=htmlspecialchars($_POST['datetime'] ?? '')?>" required />

      <label for="service">Вид услуги</label>
      <select id="service" name="service" required>
        <option value="" disabled <?= empty($_POST['service']) ? 'selected' : '' ?>>Выберите услугу</option>
        <?php foreach ($services as $service): ?>
          <option value="<?= $service['id'] ?>" <?= (isset($_POST['service']) && $_POST['service'] == $service['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($service['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="payment">Тип оплаты</label>
      <select id="payment" name="payment" required>
        <option value="" disabled <?= empty($_POST['payment']) ? 'selected' : '' ?>>Выберите тип оплаты</option>
        <option value="наличные" <?= (isset($_POST['payment']) && $_POST['payment'] == 'наличные') ? 'selected' : '' ?>>Наличные</option>
        <option value="банковская карта" <?= (isset($_POST['payment']) && $_POST['payment'] == 'банковская карта') ? 'selected' : '' ?>>Банковская карта</option>
      </select>
      <label for="other_service">Иная услуга (если нет в списке)</label>
      <input type="checkbox" id="other_service" name="other_service" onchange="toggleOtherService()">
      <textarea id="other_service_text" name="other_service_text" style="display:none;" placeholder="Опишите необходимую услугу"></textarea>

      <script>
      function toggleOtherService() {
        var checkbox = document.getElementById('other_service');
        var textarea = document.getElementById('other_service_text');
        textarea.style.display = checkbox.checked ? 'block' : 'none';
        textarea.required = checkbox.checked;
}
</script>
      <button type="submit">Отправить заявку</button>
    </form>
  </div>
</body>
</html>
