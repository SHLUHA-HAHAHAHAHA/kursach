<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../submit.php');
    exit;
}

$title = trim($_POST['title'] ?? '');

if (!$title || mb_strlen($title) > 255) {
    header('Location: ../submit.php?error=1');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO institution_suggestions (title) VALUES (?)");
$stmt->execute([$title]);

header('Location: ../submit.php?suggested=1');
exit;
