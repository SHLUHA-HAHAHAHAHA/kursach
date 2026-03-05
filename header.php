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

        <!-- Desktop nav -->
        <nav class="header-nav">
            <a href="index.php"   class="nav-link-custom <?= $current_page === 'index.php'  ? 'active' : '' ?>">
                <i class="bi bi-list-ul"></i> Обращения
            </a>
            <a href="submit.php"  class="nav-link-custom <?= $current_page === 'submit.php' ? 'active' : '' ?>">
                <i class="bi bi-pencil"></i> Подать жалобу
            </a>

            <?php if ($is_logged_in):
                $role_label = (int)$_SESSION['user_role'] === 1 ? 'Суперадмин' : 'Админ';
            ?>
                <a href="admin.php" class="nav-link-custom <?= $current_page === 'admin.php' ? 'active' : '' ?>">
                    <i class="bi bi-sliders"></i> Панель
                </a>
                <span class="nav-user">
                    <i class="bi bi-person-circle"></i>
                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                    <span class="nav-role-badge nav-role-<?= $_SESSION['user_role'] ?>"><?= $role_label ?></span>
                </span>
                <a href="scripts/logout.php" class="nav-link-custom">
                    <i class="bi bi-box-arrow-right"></i> Выйти
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-link-custom nav-cta ms-2 <?= $current_page === 'login.php' ? 'active' : '' ?>">
                    <i class="bi bi-shield-lock"></i> Войти
                </a>
            <?php endif; ?>
        </nav>

        <!-- Burger button (mobile only) -->
        <button class="burger-btn" id="burgerBtn" aria-label="Меню" aria-expanded="false">
            <i class="bi bi-list" id="burgerIcon"></i>
        </button>

    </div>

    <!-- Mobile nav drawer -->
    <nav class="mobile-nav" id="mobileNav">
        <a href="index.php"  class="mobile-nav-link <?= $current_page === 'index.php'  ? 'active' : '' ?>">
            <i class="bi bi-list-ul"></i> Обращения
        </a>
        <a href="submit.php" class="mobile-nav-link <?= $current_page === 'submit.php' ? 'active' : '' ?>">
            <i class="bi bi-pencil"></i> Подать жалобу
        </a>

        <?php if ($is_logged_in):
            $role_label = (int)$_SESSION['user_role'] === 1 ? 'Суперадмин' : 'Админ';
        ?>
            <a href="admin.php" class="mobile-nav-link <?= $current_page === 'admin.php' ? 'active' : '' ?>">
                <i class="bi bi-sliders"></i> Панель управления
            </a>
            <div class="mobile-nav-divider"></div>
            <div class="mobile-nav-user">
                <i class="bi bi-person-circle" style="font-size:16px;"></i>
                <?= htmlspecialchars($_SESSION['user_name']) ?>
                <span class="nav-role-badge nav-role-<?= $_SESSION['user_role'] ?>"><?= $role_label ?></span>
            </div>
            <a href="scripts/logout.php" class="mobile-nav-link" style="color:var(--danger);">
                <i class="bi bi-box-arrow-right"></i> Выйти
            </a>
        <?php else: ?>
            <div class="mobile-nav-divider"></div>
            <a href="login.php" class="mobile-nav-link cta">
                <i class="bi bi-shield-lock"></i> Войти в систему
            </a>
        <?php endif; ?>
    </nav>
</header>

<script>
(function () {
    const btn  = document.getElementById('burgerBtn');
    const nav  = document.getElementById('mobileNav');
    const icon = document.getElementById('burgerIcon');

    btn.addEventListener('click', function () {
        const isOpen = nav.classList.toggle('open');
        btn.setAttribute('aria-expanded', isOpen);
        icon.className = isOpen ? 'bi bi-x-lg' : 'bi bi-list';
    });

    // Закрываем при клике вне меню
    document.addEventListener('click', function (e) {
        if (!btn.contains(e.target) && !nav.contains(e.target)) {
            nav.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            icon.className = 'bi bi-list';
        }
    });
})();
</script>
