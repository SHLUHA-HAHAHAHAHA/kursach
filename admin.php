<?php
include 'scripts/connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ── Уведомления ──
$msg_type = $_GET['msg_type'] ?? '';
$msg_text = '';
switch ($_GET['msg'] ?? '') {
    case 'complaint_deleted':    $msg_text = 'Жалоба удалена.';                      $msg_type = 'success'; break;
    case 'institution_added':    $msg_text = 'Учреждение добавлено.';                $msg_type = 'success'; break;
    case 'institution_deleted':  $msg_text = 'Учреждение удалено.';                  $msg_type = 'success'; break;
    case 'suggestion_approved':  $msg_text = 'Предложение одобрено — учреждение добавлено.'; $msg_type = 'success'; break;
    case 'suggestion_rejected':  $msg_text = 'Предложение отклонено.';               $msg_type = 'success'; break;
    case 'user_added':           $msg_text = 'Администратор добавлен.';              $msg_type = 'success'; break;
    case 'user_deleted':         $msg_text = 'Пользователь удалён.';                 $msg_type = 'success'; break;
    case 'error':                $msg_text = 'Произошла ошибка. Повторите.';         $msg_type = 'danger';  break;
    case 'login_exists':         $msg_text = 'Такой логин уже занят.';               $msg_type = 'danger';  break;
    case 'forbidden':            $msg_text = 'Недостаточно прав.';                   $msg_type = 'danger';  break;
}

// ── Данные ──
$total_complaints   = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$total_institutions = $pdo->query("SELECT COUNT(*) FROM institutions")->fetchColumn();
$total_users        = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_count      = $pdo->query("SELECT COUNT(*) FROM institution_suggestions WHERE status = 'pending'")->fetchColumn();

$complaints = $pdo->query(
    "SELECT c.id, c.message, c.created_at, i.title AS institution_name
     FROM complaints c
     JOIN institutions i ON c.institution_id = i.id
     ORDER BY c.created_at DESC"
)->fetchAll();

$institutions = $pdo->query("SELECT * FROM institutions ORDER BY title")->fetchAll();

$suggestions = $pdo->query(
    "SELECT * FROM institution_suggestions ORDER BY
     CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END,
     created_at DESC"
)->fetchAll();

$users = $pdo->query(
    "SELECT id, name, login, role, created_at FROM users ORDER BY role DESC, name"
)->fetchAll();

$section  = $_GET['section'] ?? 'overview';
$is_super = (int)$_SESSION['user_role'] === 1;

$page_title = 'Панель управления — ГолосОбразования';
include 'header.php';
?>

<div class="admin-layout">

    <!-- ── SIDEBAR ── -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Управление</div>

        <button class="admin-nav-link <?= $section === 'overview'     ? 'active' : '' ?>"
                onclick="showSection('overview')">
            <i class="bi bi-grid-1x2"></i> Обзор
        </button>
        <button class="admin-nav-link <?= $section === 'complaints'   ? 'active' : '' ?>"
                onclick="showSection('complaints')">
            <i class="bi bi-chat-left-text"></i> Жалобы
            <span class="ms-auto badge bg-secondary rounded-pill" style="font-size:10px"><?= $total_complaints ?></span>
        </button>
        <button class="admin-nav-link <?= $section === 'institutions' ? 'active' : '' ?>"
                onclick="showSection('institutions')">
            <i class="bi bi-building"></i> Учреждения
        </button>
        <button class="admin-nav-link <?= $section === 'suggestions'  ? 'active' : '' ?>"
                onclick="showSection('suggestions')">
            <i class="bi bi-lightbulb"></i> Предложения
            <?php if ($pending_count > 0): ?>
            <span class="ms-auto pending-count-badge"><?= $pending_count ?></span>
            <?php endif; ?>
        </button>

        <?php if ($is_super): ?>
        <div class="admin-nav-divider"></div>
        <div class="admin-sidebar-title">Суперадмин</div>
        <button class="admin-nav-link <?= $section === 'users' ? 'active' : '' ?>"
                onclick="showSection('users')">
            <i class="bi bi-people"></i> Пользователи
        </button>
        <?php endif; ?>
    </aside>

    <!-- ── MAIN ── -->
    <main>

        <?php if ($msg_text): ?>
        <div class="admin-alert admin-alert-<?= $msg_type ?>">
            <i class="bi bi-<?= $msg_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= htmlspecialchars($msg_text) ?>
        </div>
        <?php endif; ?>

        <!-- ════ ОБЗОР ════ -->
        <div class="admin-section <?= $section === 'overview' ? 'active' : '' ?>" id="section-overview">
            <div class="admin-section-title">Обзор</div>
            <div class="admin-section-desc">Общая статистика системы.</div>

            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="num"><?= $total_complaints ?></div>
                    <div class="lbl">Жалоб</div>
                </div>
                <div class="admin-stat-card">
                    <div class="num"><?= $total_institutions ?></div>
                    <div class="lbl">Учреждений</div>
                </div>
                <div class="admin-stat-card">
                    <div class="num" style="<?= $pending_count > 0 ? 'color:#f59e0b' : '' ?>"><?= $pending_count ?></div>
                    <div class="lbl">Ожидают проверки</div>
                </div>
            </div>

            <div class="admin-section-title" style="font-size:16px; margin-bottom:12px;">Последние жалобы</div>
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Учреждение</th><th>Сообщение</th><th>Дата</th></tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($complaints, 0, 5) as $c): ?>
                    <tr>
                        <td style="color:var(--muted); font-size:12px;"><?= $c['id'] ?></td>
                        <td class="td-main"><?= htmlspecialchars($c['institution_name']) ?></td>
                        <td class="td-message"><?= htmlspecialchars($c['message']) ?></td>
                        <td style="font-size:12px; white-space:nowrap;"><?= date('d.m.Y', strtotime($c['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ════ ЖАЛОБЫ ════ -->
        <div class="admin-section <?= $section === 'complaints' ? 'active' : '' ?>" id="section-complaints">
            <div class="admin-section-title">Жалобы</div>
            <div class="admin-section-desc">Просмотр и удаление обращений граждан.</div>

            <?php if (empty($complaints)): ?>
                <div class="empty-state"><i class="bi bi-inbox"></i><p>Жалоб пока нет.</p></div>
            <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Учреждение</th><th>Сообщение</th><th>Дата</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td style="color:var(--muted); font-size:12px;"><?= $c['id'] ?></td>
                        <td class="td-main"><?= htmlspecialchars($c['institution_name']) ?></td>
                        <td class="td-message"><?= htmlspecialchars($c['message']) ?></td>
                        <td style="font-size:12px; white-space:nowrap;"><?= date('d.m.Y, H:i', strtotime($c['created_at'])) ?></td>
                        <td>
                            <a href="scripts/admin_actions.php?action=delete_complaint&id=<?= $c['id'] ?>"
                               class="btn-action-danger"
                               onclick="return confirm('Удалить эту жалобу?')">
                                <i class="bi bi-trash3"></i> Удалить
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- ════ УЧРЕЖДЕНИЯ ════ -->
        <div class="admin-section <?= $section === 'institutions' ? 'active' : '' ?>" id="section-institutions">
            <div class="admin-section-title">Учреждения</div>
            <div class="admin-section-desc">Добавление и удаление учебных заведений.</div>

            <div class="admin-add-card">
                <div class="card-label"><i class="bi bi-plus-circle"></i> Добавить учреждение</div>
                <form action="scripts/admin_actions.php" method="POST" class="d-flex gap-2 flex-wrap">
                    <input type="hidden" name="action" value="add_institution">
                    <input type="text" name="title" class="form-control" placeholder="Название учебного заведения…" required style="max-width:440px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Добавить</button>
                </form>
            </div>

            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Название</th><th>Жалоб</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($institutions as $inst):
                        $cnt = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE institution_id = ?");
                        $cnt->execute([$inst['id']]);
                        $c_count = $cnt->fetchColumn();
                    ?>
                    <tr>
                        <td style="color:var(--muted); font-size:12px;"><?= $inst['id'] ?></td>
                        <td class="td-main"><?= htmlspecialchars($inst['title']) ?></td>
                        <td><span class="badge bg-light text-secondary border" style="font-size:12px;"><?= $c_count ?></span></td>
                        <td>
                            <?php if ($c_count == 0): ?>
                            <a href="scripts/admin_actions.php?action=delete_institution&id=<?= $inst['id'] ?>"
                               class="btn-action-danger"
                               onclick="return confirm('Удалить «<?= htmlspecialchars(addslashes($inst['title'])) ?>»?')">
                                <i class="bi bi-trash3"></i> Удалить
                            </a>
                            <?php else: ?>
                            <span style="font-size:12px; color:var(--muted);" title="Сначала удалите все жалобы">
                                <i class="bi bi-lock"></i> Есть жалобы
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ════ ПРЕДЛОЖЕНИЯ ════ -->
        <div class="admin-section <?= $section === 'suggestions' ? 'active' : '' ?>" id="section-suggestions">
            <div class="admin-section-title">Предложения учреждений</div>
            <div class="admin-section-desc">
                Пользователи предложили добавить эти учебные заведения.
                <?php if ($pending_count > 0): ?>
                <span class="pending-count-badge ms-1"><?= $pending_count ?></span> ожидают решения.
                <?php endif; ?>
            </div>

            <?php if (empty($suggestions)): ?>
                <div class="empty-state"><i class="bi bi-lightbulb"></i><p>Предложений пока нет.</p></div>
            <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Название</th><th>Дата</th><th>Статус</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($suggestions as $s): ?>
                    <tr>
                        <td style="color:var(--muted); font-size:12px;"><?= $s['id'] ?></td>
                        <td class="td-main"><?= htmlspecialchars($s['title']) ?></td>
                        <td style="font-size:12px; white-space:nowrap;"><?= date('d.m.Y', strtotime($s['created_at'])) ?></td>
                        <td>
                            <?php if ($s['status'] === 'pending'): ?>
                                <span class="status-badge status-pending"><i class="bi bi-hourglass-split me-1"></i>На рассмотрении</span>
                            <?php elseif ($s['status'] === 'approved'): ?>
                                <span class="status-badge status-approved"><i class="bi bi-check-circle me-1"></i>Одобрено</span>
                            <?php else: ?>
                                <span class="status-badge status-rejected"><i class="bi bi-x-circle me-1"></i>Отклонено</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($s['status'] === 'pending'): ?>
                            <div class="d-flex gap-1">
                                <a href="scripts/admin_actions.php?action=approve_suggestion&id=<?= $s['id'] ?>"
                                   class="btn-action-success"
                                   onclick="return confirm('Одобрить и добавить «<?= htmlspecialchars(addslashes($s['title'])) ?>» в список учреждений?')">
                                    <i class="bi bi-check-lg"></i> Принять
                                </a>
                                <a href="scripts/admin_actions.php?action=reject_suggestion&id=<?= $s['id'] ?>"
                                   class="btn-action-danger"
                                   onclick="return confirm('Отклонить это предложение?')">
                                    <i class="bi bi-x-lg"></i> Отклонить
                                </a>
                            </div>
                            <?php else: ?>
                            <a href="scripts/admin_actions.php?action=delete_suggestion&id=<?= $s['id'] ?>"
                               class="btn-action-danger"
                               onclick="return confirm('Удалить запись?')"
                               style="opacity:0.6;">
                                <i class="bi bi-trash3"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- ════ ПОЛЬЗОВАТЕЛИ (суперадмин) ════ -->
        <?php if ($is_super): ?>
        <div class="admin-section <?= $section === 'users' ? 'active' : '' ?>" id="section-users">
            <div class="admin-section-title">Пользователи</div>
            <div class="admin-section-desc">Управление администраторами системы.</div>

            <div class="admin-add-card">
                <div class="card-label"><i class="bi bi-person-plus"></i> Добавить администратора</div>
                <form action="scripts/admin_actions.php" method="POST">
                    <input type="hidden" name="action" value="add_user">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Имя</label>
                            <input type="text" name="name" class="form-control" placeholder="Иванов И.И." required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" placeholder="ivanov" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" placeholder="Минимум 6 символов" required minlength="6">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Роль</label>
                            <select name="role" class="form-select">
                                <option value="0">Админ</option>
                                <option value="1">Суперадмин</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Имя</th><th>Логин</th><th>Роль</th><th>Добавлен</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="color:var(--muted); font-size:12px;"><?= $u['id'] ?></td>
                        <td class="td-main"><?= htmlspecialchars($u['name']) ?></td>
                        <td style="font-family:monospace; font-size:13px;"><?= htmlspecialchars($u['login']) ?></td>
                        <td>
                            <span class="role-badge role-badge-<?= $u['role'] ?>">
                                <?= (int)$u['role'] === 1 ? 'Суперадмин' : 'Админ' ?>
                            </span>
                        </td>
                        <td style="font-size:12px; white-space:nowrap;"><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <a href="scripts/admin_actions.php?action=delete_user&id=<?= $u['id'] ?>"
                               class="btn-action-danger"
                               onclick="return confirm('Удалить пользователя «<?= htmlspecialchars(addslashes($u['login'])) ?>»?')">
                                <i class="bi bi-trash3"></i> Удалить
                            </a>
                            <?php else: ?>
                            <span style="font-size:12px; color:var(--muted);">— вы</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showSection(name) {
    document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.admin-nav-link').forEach(b => b.classList.remove('active'));
    const section = document.getElementById('section-' + name);
    if (section) section.classList.add('active');
    document.querySelectorAll('.admin-nav-link').forEach(b => {
        if (b.getAttribute('onclick') === `showSection('${name}')`) b.classList.add('active');
    });
}
showSection('<?= htmlspecialchars($section) ?>');
</script>
</body>
</html>
