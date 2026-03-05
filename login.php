<?php
include 'scripts/connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Уже вошёл — редирект
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = (int) $user['role']; // 0 = админ, 1 = суперадмин
            header('Location: index.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль.';
        }
    } else {
        $error = 'Заполните все поля.';
    }
}

$page_title = 'Вход — ГолосОбразования';
include 'header.php';
?>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h2>Вход в систему</h2>
        <p class="login-sub">Доступ только для сотрудников и администраторов</p>

        <?php if ($error): ?>
        <div class="alert alert-danger rounded-3 border-0 py-2 px-3 mb-3" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label" for="login">Логин</label>
                <input
                    type="text"
                    name="login"
                    id="login"
                    class="form-control"
                    placeholder="Введите логин"
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                    required
                    autofocus
                >
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Пароль</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Введите пароль"
                    required
                >
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
            </button>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
