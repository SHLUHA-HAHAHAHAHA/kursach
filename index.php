<?php
include 'scripts/connect.php';
$page_title = 'Обращения — ГолосОбразования';
include 'header.php';

$total              = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$institutions_count = $pdo->query("SELECT COUNT(DISTINCT institution_id) FROM complaints")->fetchColumn();

$stmt       = $pdo->query(
    "SELECT c.id, c.message, c.created_at, i.title AS institution_name
     FROM complaints c
     JOIN institutions i ON c.institution_id = i.id
     ORDER BY c.created_at DESC"
);
$complaints = $stmt->fetchAll();
?>

<div class="page-wrapper">

    <h1 class="page-heading">Обращения граждан</h1>
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

    <!-- List -->
    <?php if (empty($complaints)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Обращений пока нет. <a href="submit.php">Будьте первым.</a></p>
        </div>
    <?php else: ?>
        <?php foreach ($complaints as $i => $row): ?>
        <div class="complaint-card" style="animation-delay:<?= $i * 0.04 ?>s">

            <div class="complaint-institution">
                <i class="bi bi-building me-2"></i><?= htmlspecialchars($row['institution_name']) ?>
            </div>

            <p class="complaint-message"><?= nl2br(htmlspecialchars($row['message'])) ?></p>

            <div class="complaint-meta">
                <i class="bi bi-clock"></i>
                <?= date('d.m.Y, H:i', strtotime($row['created_at'])) ?>
                <span class="separator mx-2">·</span>
            </div>

        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
