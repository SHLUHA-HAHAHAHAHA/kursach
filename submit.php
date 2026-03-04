<?php
include 'scripts/connect.php';
$page_title = 'Подать жалобу — ГолосОбразования';
include 'header.php';

$success = isset($_GET['sent']) && $_GET['sent'] == '1';
$error   = isset($_GET['error']) && $_GET['error'] == '1';
?>

<div class="page-wrapper">

    <div class="page-eyebrow">Ваш голос важен</div>
    <h1 class="page-heading">Подать обращение</h1>
    <p class="page-desc">Заполните форму ниже. Ваши данные не сохраняются — обращение полностью анонимно.</p>

    <?php if ($success): ?>
    <div class="alert-success-custom">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            <div class="alert-title">Обращение принято!</div>
            <div class="alert-text">Ваша жалоба добавлена в список обращений. Спасибо за активную гражданскую позицию.</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        Ошибка при отправке. Пожалуйста, проверьте данные и попробуйте снова.
    </div>
    <?php endif; ?>

    <div class="form-card">
        <form action="scripts/save.php" method="POST" novalidate>

            <div class="row g-4 mb-2">
                <div class="col-md-7">
                    <label class="form-label" for="institution_id">
                        <i class="bi bi-bank me-1"></i>Учебное заведение
                    </label>
                    <select name="institution_id" id="institution_id" required class="form-select">
                        <option value="" disabled selected>Выберите учреждение…</option>
                        <?php
                        $institutions = $pdo->query("SELECT * FROM institutions ORDER BY title");
                        foreach ($institutions as $inst):
                        ?>
                        <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="category">
                        <i class="bi bi-tag me-1"></i>Категория жалобы
                    </label>
                    <select name="category" id="category" class="form-select" >
                        <option value="Качество обучения">📚 Качество обучения</option>
                        <option value="Инфраструктура">🏛 Инфраструктура</option>
                        <option value="Коррупция">⚠️ Коррупция</option>
                        <option value="Другое">💬 Другое</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="message">
                    <i class="bi bi-chat-left-text me-1"></i>Суть проблемы
                </label>
                <textarea 
                    name="message"
                    id="message"
                    class="form-control"
                    rows="5"
                    required
                    placeholder="Опишите ситуацию подробно. Чем точнее описание — тем выше шанс, что проблема будет решена."
                ></textarea>
            </div>

            <hr class="text-muted opacity-25 my-4">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Отправить обращение
                </button>
                <a href="index.php" class="btn-link-back">
                    ← Вернуться к списку
                </a>
            </div>

            <div class="anon-note mt-3">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Обращение отправляется <strong>анонимно</strong>. Ваши личные данные, IP-адрес и устройство не фиксируются.</span>
            </div>

        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
