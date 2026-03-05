<?php
$current_page = basename($_SERVER['PHP_SELF']);
if (session_status() === PHP_SESSION_NONE) session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'ГолосОбразования' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="header-inner">

        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="bi bi-journal-text"></i></div>
            <div class="logo-text">
                <span class="logo-title">ГолосОбразования</span>
                <span class="logo-sub">Система обращений</span>
            </div>
        </a>

        <nav class="d-flex align-items-center gap-1">
            <a href="index.php"
               class="nav-link-custom <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-list-ul"></i> Обращения
            </a>
            <a href="submit.php"
               class="nav-link-custom <?= $current_page === 'submit.php' ? 'active' : '' ?>">
                <i class="bi bi-pencil"></i> Подать жалобу
            </a>

            <?php if ($is_logged_in): ?>
            <?php
                $role_label = $_SESSION['user_role'] === 1 ? 'Суперадмин' : 'Админ';
            ?>
                <a href="admin.php"
                   class="nav-link-custom <?= $current_page === 'admin.php' ? 'active' : '' ?>">
                    <i class="bi bi-sliders"></i> Панель
                </a>
                <span class="nav-user">
                    <i class="bi bi-person-circle"></i>
                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                    <span class="nav-role-badge nav-role-<?= $_SESSION['user_role'] ?>">
                        <?= $role_label ?>
                    </span>
                </span>
                <a href="scripts/logout.php" class="nav-link-custom">
                    <i class="bi bi-box-arrow-right"></i> Выйти
                </a>
            <?php else: ?>
                <a href="login.php"
                   class="nav-link-custom nav-cta ms-2 <?= $current_page === 'login.php' ? 'active' : '' ?>">
                    <i class="bi bi-shield-lock"></i> Войти
                </a>
            <?php endif; ?>
        </nav>

    </div>
</header>
