<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../submit.php');
    exit;
}

$institution_id = (int) ($_POST['institution_id'] ?? 0);
$category       = trim($_POST['category'] ?? '');
$message        = trim($_POST['message'] ?? '');

$allowed_categories = ['Качество обучения', 'Инфраструктура', 'Коррупция', 'Другое'];

if (!$institution_id || !$message || !in_array($category, $allowed_categories)) {
    header('Location: ../submit.php?error=1');
    exit;
}

$check = $pdo->prepare("SELECT id FROM institutions WHERE id = ?");
$check->execute([$institution_id]);
if (!$check->fetch()) {
    header('Location: ../submit.php?error=1');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO complaints (institution_id, category, message) VALUES (?, ?, ?)"
);
$stmt->execute([$institution_id, $category, $message]);

header('Location: ../submit.php?sent=1');
exit;
