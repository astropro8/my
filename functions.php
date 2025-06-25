<?php
// functions.php
session_start();

require_once 'config.php';

// Проверка, авторизован ли пользователь
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Получить данные текущего пользователя
function currentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Авторизация пользователя по логину и паролю
function loginUser($login, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

// Регистрация нового пользователя
function registerUser($fullname, $phone, $email, $login, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
    $stmt->execute([$login, $email]);
    if ($stmt->fetch()) {
        return false; // Пользователь с таким логином или email уже есть
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (fullname, phone, email, login, password_hash) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$fullname, $phone, $email, $login, $hash]);
}

// Получить заявки текущего пользователя
function getUserRequests($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.*, s.name as service_name
        FROM requests r
        JOIN services s ON r.service_id = s.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Добавить новую заявку
function addRequest($user_id, $address, $phone, $service_id, $service_date, $payment_type) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO requests (user_id, address, phone, service_id, service_date, payment_type)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$user_id, $address, $phone, $service_id, $service_date, $payment_type]);
}
