<?php
include 'connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// ── Защита: только залогиненные ──
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$action   = $_POST['action'] ?? $_GET['action'] ?? '';
$is_super = (int)$_SESSION['user_role'] === 1;

function redirect(string $section, string $msg): void {
    header("Location: ../admin.php?section={$section}&msg={$msg}");
    exit;
}

switch ($action) {

    // ══════════════════════════════════════
    //  Удалить жалобу  (доступно всем админам)
    // ══════════════════════════════════════
    case 'delete_complaint': {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('complaints', 'error');

        $stmt = $pdo->prepare("DELETE FROM complaints WHERE id = ?");
        $stmt->execute([$id]);
        redirect('complaints', 'complaint_deleted');
    }

    // ══════════════════════════════════════
    //  Добавить учреждение  (доступно всем админам)
    // ══════════════════════════════════════
    case 'add_institution': {
        $title = trim($_POST['title'] ?? '');
        if (!$title) redirect('institutions', 'error');

        $stmt = $pdo->prepare("INSERT INTO institutions (title) VALUES (?)");
        $stmt->execute([$title]);
        redirect('institutions', 'institution_added');
    }

    // ══════════════════════════════════════
    //  Удалить учреждение  (доступно всем админам)
    //  Только если нет связанных жалоб
    // ══════════════════════════════════════
    case 'delete_institution': {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('institutions', 'error');

        // Проверяем — есть ли жалобы
        $check = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE institution_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) redirect('institutions', 'error');

        $stmt = $pdo->prepare("DELETE FROM institutions WHERE id = ?");
        $stmt->execute([$id]);
        redirect('institutions', 'institution_deleted');
    }

    // ══════════════════════════════════════
    //  Одобрить предложение — создаёт учреждение и меняет статус
    // ══════════════════════════════════════
    case 'approve_suggestion': {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('suggestions', 'error');

        $suggestion = $pdo->prepare("SELECT * FROM institution_suggestions WHERE id = ? AND status = 'pending'");
        $suggestion->execute([$id]);
        $row = $suggestion->fetch();
        if (!$row) redirect('suggestions', 'error');

        // Добавляем в institutions
        $ins = $pdo->prepare("INSERT INTO institutions (title) VALUES (?)");
        $ins->execute([$row['title']]);

        // Обновляем статус предложения
        $upd = $pdo->prepare("UPDATE institution_suggestions SET status = 'approved' WHERE id = ?");
        $upd->execute([$id]);

        redirect('suggestions', 'suggestion_approved');
    }

    // ══════════════════════════════════════
    //  Отклонить предложение
    // ══════════════════════════════════════
    case 'reject_suggestion': {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('suggestions', 'error');

        $upd = $pdo->prepare("UPDATE institution_suggestions SET status = 'rejected' WHERE id = ?");
        $upd->execute([$id]);
        redirect('suggestions', 'suggestion_rejected');
    }

    // ══════════════════════════════════════
    //  Удалить запись предложения (уже обработанного)
    // ══════════════════════════════════════
    case 'delete_suggestion': {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('suggestions', 'error');

        $pdo->prepare("DELETE FROM institution_suggestions WHERE id = ? AND status != 'pending'")->execute([$id]);
        redirect('suggestions', 'suggestion_rejected');
    }


    case 'add_user': {
        if (!$is_super) redirect('overview', 'forbidden');

        $name     = trim($_POST['name']     ?? '');
        $login    = trim($_POST['login']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = (int)($_POST['role']    ?? 0);

        if (!$name || !$login || strlen($password) < 6) redirect('users', 'error');
        if (!in_array($role, [0, 1], true)) redirect('users', 'error');

        // Проверяем уникальность логина
        $check = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $check->execute([$login]);
        if ($check->fetch()) redirect('users', 'login_exists');

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, login, password_hash, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $login, $hash, $role]);
        redirect('users', 'user_added');
    }

    // ══════════════════════════════════════
    //  Удалить пользователя  (только суперадмин)
    //  Нельзя удалить себя
    // ══════════════════════════════════════
    case 'delete_user': {
        if (!$is_super) redirect('overview', 'forbidden');

        $id = (int)($_GET['id'] ?? 0);
        if (!$id || $id === (int)$_SESSION['user_id']) redirect('users', 'error');

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        redirect('users', 'user_deleted');
    }

    default:
        header('Location: ../admin.php');
        exit;
}
