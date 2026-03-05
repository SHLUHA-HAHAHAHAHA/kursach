<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../submit.php');
    exit;
}

$institution_id = (int) ($_POST['institution_id'] ?? 0);
$message        = trim($_POST['message'] ?? '');

if (!$institution_id || !$message) {
    header('Location: ../submit.php?error=1');
    exit;
}

$check = $pdo->prepare("SELECT id FROM institutions WHERE id = ?");
$check->execute([$institution_id]);
if (!$check->fetch()) {
    header('Location: ../submit.php?error=1');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO complaints (institution_id, message) VALUES (?, ?)");
$stmt->execute([$institution_id, $message]);

header('Location: ../submit.php?sent=1');
exit;
