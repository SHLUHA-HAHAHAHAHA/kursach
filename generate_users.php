<?php
/**
 * generate_users.php
 * Запустите один раз через браузер или CLI для создания пользователей с реальными паролями.
 * После использования — УДАЛИТЕ этот файл с сервера.
 *
 * CLI: php generate_users.php
 * Web: http://localhost/generate_users.php
 */

include 'scripts/connect.php';

$users = [
    [
        'name'     => 'Администратор',
        'login'    => 'admin',
        'password' => 'admin123',   // ← смените перед деплоем
        'role'     => 0,            // 0 = админ
    ],
    [
        'name'     => 'Суперадминов С.С.',
        'login'    => 'superadmin',
        'password' => 'super123',   // ← смените перед деплоем
        'role'     => 1,            // 1 = суперадмин
    ],
];

$stmt = $pdo->prepare(
    "INSERT INTO users (name, login, password_hash, role)
     VALUES (:name, :login, :hash, :role)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), name = VALUES(name)"
);

foreach ($users as $u) {
    $hash = password_hash($u['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt->execute([
        ':name'  => $u['name'],
        ':login' => $u['login'],
        ':hash'  => $hash,
        ':role'  => $u['role'],
    ]);
    echo "✓ Пользователь «{$u['login']}» создан.\n";
}

echo "\nГотово! Удалите этот файл с сервера.\n";
