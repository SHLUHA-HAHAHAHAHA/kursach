<?php
include 'scripts/connect.php';
$page_title = 'Обращения — ГолосОбразования';
include 'header.php';

// ── Параметры фильтрации ──
$search      = trim($_GET['q']    ?? '');
$filter_inst = (int)($_GET['inst'] ?? 0);

// ── Статистика ──
$total              = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$institutions_count = $pdo->query("SELECT COUNT(DISTINCT institution_id) FROM complaints")->fetchColumn();

// ── Список учреждений для фильтра (только те, у которых есть жалобы) ──
$institutions = $pdo->query(
    "SELECT i.id, i.title
     FROM institutions i
     WHERE EXISTS (SELECT 1 FROM complaints c WHERE c.institution_id = i.id)
     ORDER BY i.title"
)->fetchAll();

// ── Основной запрос с фильтрами ──
$where  = [];
$params = [];

if ($filter_inst > 0) {
    $where[]  = "c.institution_id = ?";
    $params[] = $filter_inst;
}

if ($search !== '') {
    $where[]  = "c.message LIKE ?";
    $params[] = '%' . $search . '%';
}

$sql = "SELECT c.id, c.message, c.created_at, i.title AS institution_name, i.id AS institution_id
        FROM complaints c
        JOIN institutions i ON c.institution_id = i.id";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY c.created_at DESC";

$stmt       = $pdo->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

$found = count($complaints);
$has_filters = $search !== '' || $filter_inst > 0;


?>

<div class="page-wrapper">

    <h1 class="page-heading">Обращения студентов</h1>
    <p class="page-desc">Анонимные жалобы на учебные заведения.</p>

    <!-- Stats -->
    <div class="stats-bar d-flex mb-4">
        <div class="stat-item">
            <div class="stat-num"><?= $total ?></div>
            <div class="stat-label">Всего обращений</div>
        </div>
        <div class="stat-item">
            <div class="stat-num"><?= $institutions_count ?></div>
            <div class="stat-label">Учреждений упомянуто</div>
        </div>
    </div>

    <!-- Search & Filter bar -->
    <form method="GET" action="index.php" class="filter-search-bar" id="filterForm">

        <!-- Поиск по тексту -->
        <div class="search-input-wrap">
            <i class="bi bi-search"></i>
            <input
                type="text"
                name="q"
                class="search-input"
                placeholder="Поиск по тексту жалобы…"
                value="<?= htmlspecialchars($search) ?>"
                autocomplete="off"
                id="searchInput"
            >
        </div>

        <!-- Фильтр по учреждению -->
        <div class="filter-select-wrap">
            <i class="bi bi-building"></i>
            <select
                name="inst"
                class="filter-select <?= $filter_inst > 0 ? 'has-value' : '' ?>"
                id="instSelect"
                onchange="this.form.submit()"
            >
                <option value="0">Все учреждения</option>
                <?php foreach ($institutions as $inst): ?>
                <option value="<?= $inst['id'] ?>"
                    <?= $filter_inst === (int)$inst['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($inst['title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Кнопка сброса — только когда есть активные фильтры -->
        <?php if ($has_filters): ?>
        <a href="index.php" class="btn-reset-filter">
            <i class="bi bi-x-circle"></i> Сбросить
        </a>
        <?php endif; ?>

    </form>

    <!-- Счётчик результатов -->
    <?php if ($has_filters): ?>
    <div class="results-count">
        <i class="bi bi-funnel-fill" style="color:var(--accent); font-size:13px;"></i>
        Найдено: <strong><?= $found ?></strong>
        <?php if ($found !== $total): ?>
        из <?= $total ?>
        <?php endif; ?>
        <?php if ($search): ?>
        — по запросу «<strong><?= htmlspecialchars($search) ?></strong>»
        <?php endif; ?>
        <?php if ($filter_inst > 0):
            $inst_name = '';
            foreach ($institutions as $i) { if ((int)$i['id'] === $filter_inst) { $inst_name = $i['title']; break; } }
        ?>
        <?php if ($inst_name): ?>
        — учреждение «<strong><?= htmlspecialchars($inst_name) ?></strong>»
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- List -->
    <?php if (empty($complaints)): ?>
        <div class="empty-state">
            <i class="bi bi-<?= $has_filters ? 'search' : 'inbox' ?>"></i>
            <?php if ($has_filters): ?>
                <p>Ничего не найдено. <a href="index.php">Сбросить фильтры</a></p>
            <?php else: ?>
                <p>Обращений пока нет. <a href="submit.php">Будьте первым.</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($complaints as $idx => $row): ?>
        <div class="complaint-card" style="animation-delay:<?= $idx * 0.04 ?>s">

            <div class="complaint-institution">
                <i class="bi bi-building me-2"></i>
                <?php if ($filter_inst === 0): ?>
                    <!-- Кликабельное название — фильтрует по учреждению -->
                    <a href="?inst=<?= $row['institution_id'] ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
                       style="color:inherit; text-decoration:none; border-bottom: 1px dotted var(--accent);"
                       title="Показать только это учреждение">
                        <?= htmlspecialchars($row['institution_name']) ?>
                    </a>
                <?php else: ?>
                    <?= htmlspecialchars($row['institution_name']) ?>
                <?php endif; ?>
            </div>

            <p class="complaint-message">
                <?= $row['message']?>
            </p>

            <div class="complaint-meta">
                <i class="bi bi-clock"></i>
                <?= date('d.m.Y, H:i', strtotime($row['created_at'])) ?>
                <span class="separator mx-2">·</span>
                <i class="bi bi-shield-check text-success-custom"></i>
                <span class="text-success-custom">Анонимно</span>
            </div>

        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Отправка формы при вводе с небольшой задержкой (debounce)
(function () {
    const input = document.getElementById('searchInput');
    const form  = document.getElementById('filterForm');
    let timer;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            form.submit();
        }, 450);
    });
})();
</script>
</body>
</html>
