<?php
$newPassword = 'admin'; // замените на желаемый пароль
echo password_hash($newPassword, PASSWORD_DEFAULT);