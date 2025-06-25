<?php
// config.php
$host = 'MySQL-8.0';
$db   = 'myne_sam_db';    // имя вашей базы данных
$user = 'root';           // ваш пользователь базы
$pass = '';               // ваш пароль базы
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (PDOException $e) {
    exit('Ошибка подключения к базе данных: ' . $e->getMessage());
}
