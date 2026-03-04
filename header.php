<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'ГолосОбразования' ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Mulish:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Custom styles -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="header-inner">

        <a href="index.php" class="logo">
            <div class="logo-text">
                <span class="logo-title">ГолосОбразования</span>
                <span class="logo-sub">Система жалоб</span>
            </div>
        </a>

        <nav class="d-flex align-items-center gap-1">
            <a href="index.php"
               class="nav-link-custom <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-list-ul"></i> Обращения
            </a>
            <a href="submit.php"
               class="nav-link-custom nav-cta <?= $current_page === 'submit.php' ? 'active' : '' ?>">
                <i class="bi bi-pencil-square"></i> Подать жалобу
            </a>
        </nav>

    </div>
</header>
