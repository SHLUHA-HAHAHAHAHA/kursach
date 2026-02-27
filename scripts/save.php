<?php
include 'connect.php';
include 'validator.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['institution_name'];
    $cat  = $_POST['category'];
    $msg  = $_POST['message'];

    if (!empty($name) && !empty($msg) && is_valid_message($msg)) {
        $sql = "INSERT INTO complaints (institution_name, category, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $cat, $msg]);
    }

    header("Location: ../index.php"); // Возвращаемся обратно
    exit();
}
?>