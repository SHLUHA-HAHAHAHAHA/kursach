<?php
include 'scripts/connect.php';
$page_title = 'Обращения — ГолосОбразования';
include 'header.php';

$total              = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$institutions_count = $pdo->query("SELECT COUNT(DISTINCT institution_id) FROM complaints")->fetchColumn();
$categories         = ['Качество обучения', 'Инфраструктура', 'Коррупция', 'Другое'];

$category_icons = [
    'Качество обучения' => 'bi-book',
    'Инфраструктура'    => 'bi-building',
    'Коррупция'         => 'bi-exclamation-triangle',
    'Другое'            => 'bi-three-dots',
];

$filter   = $_GET['cat'] ?? '';
$base_sql = "SELECT c.*, i.title AS institution_name
             FROM complaints c
             JOIN institutions i ON c.institution_id = i.id";

if ($filter && $filter !== 'all') {
    $stmt = $pdo->prepare("$base_sql WHERE c.category = ? ORDER BY c.created_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("$base_sql ORDER BY c.created_at DESC");
}
$complaints = $stmt->fetchAll();
?>

<div class="page-wrapper">

    <div class="page-eyebrow">Народный контроль</div>
    <h1 class="page-heading">Обращения граждан</h1>
    <p class="page-desc">Анонимные жалобы на учебные заведения. Каждый голос важен.</p>

    <!-- Stats bar -->
    <div class="stats-bar d-flex">
        <div class="stat-item">
            <div class="stat-num"><?= $total ?></div>
            <div class="stat-label">Всего обращений</div>
        </div>
        <div class="stat-item">
            <div class="stat-num"><?= $institutions_count ?></div>
            <div class="stat-label">Учреждений упомянуто</div>
        </div>
        <div class="stat-item">
            <div class="stat-num"><?= count($categories) ?></div>
            <div class="stat-label">Категории</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
        <button class="filter-btn <?= (!$filter || $filter === 'all') ? 'active' : '' ?>"
                onclick="location.href='index.php'">
            Все
        </button>
        <?php foreach ($categories as $cat): ?>
        <button class="filter-btn <?= $filter === $cat ? 'active' : '' ?>"
                onclick="location.href='index.php?cat=<?= urlencode($cat) ?>'">
            <?= htmlspecialchars($cat) ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- List -->
    <?php if (empty($complaints)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Обращений пока нет. <a href="submit.php">Будьте первым.</a></p>
        </div>
    <?php else: ?>
        <?php foreach ($complaints as $i => $row): ?>
        <div class="complaint-card" style="animation-delay:<?= $i * 0.05 ?>s">

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div class="complaint-institution">
                    <i class="bi bi-bank me-2"></i><?= htmlspecialchars($row['institution_name']) ?>
                </div>
                <span class="badge-category">
                    <i class="bi <?= $category_icons[$row['category']] ?? 'bi-tag' ?> me-1"></i>
                    <?= htmlspecialchars($row['category']) ?>
                </span>
            </div>

            <p class="complaint-message"><?= nl2br(htmlspecialchars($row['message'])) ?></p>

            <div class="complaint-meta">
                <i class="bi bi-clock"></i>
                <?= htmlspecialchars($row['created_at']) ?>
                <span class="separator mx-2">|</span>
                <i class="bi bi-shield-check text-success-custom"></i>
                <span class="text-success-custom">Анонимно</span>
            </div>

        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
