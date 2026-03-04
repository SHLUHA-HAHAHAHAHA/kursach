<?php include 'scripts/connect.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Жалобы на учебные заведения</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .complaint-card { margin-bottom: 20px; border-left: 5px solid #dc3545; }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="mb-4 text-center">Система подачи жалоб</h2>

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title">Оставить жалобу</h5>
            <form action="scripts/save.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Учебное заведение</label>
                        <select name="institution_id" required class="form-select">
                            <?php $institutions = $pdo->query("SELECT * FROM institutions");
                                foreach($institutions as $institution): ?>
                                <option value="<?=$institution['id']?>"><?=$institution['title']?></option>
                                <?endforeach?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Категория</label>
                        <select name="category" class="form-select">
                            <option value="Качество обучения">Качество обучения</option>
                            <option value="Инфраструктура">Инфраструктура</option>
                            <option value="Коррупция">Коррупция</option>
                            <option value="Другое">Другое</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Суть проблемы</label>
                    <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить анонимно</button>
            </form>
        </div>
    </div>

    <hr>

    <h3 class="mb-4">Обращения</h3>
    <div class="row">
        <?php
        $stmt = $pdo->query("SELECT * FROM complaints ORDER BY created_at DESC");
        while ($row = $stmt->fetch()): ?>
            <div class="col-12">
                <div class="card complaint-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title text-primary"><?= $row['institution_name'] ?></h5>
                            <span class="badge bg-secondary"><?= $row['category'] ?></span>
                        </div>
                        <p class="card-text mt-2"><?= $row['message'] ?></p>
                        <small class="text-muted">Дата публикации: <?= $row['created_at'] ?></small>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>