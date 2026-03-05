<?php
include 'scripts/connect.php';
$page_title = 'Подать жалобу — ГолосОбразования';
include 'header.php';

$success          = isset($_GET['sent'])      && $_GET['sent']      == '1';
$error            = isset($_GET['error'])     && $_GET['error']     == '1';
$suggest_sent     = isset($_GET['suggested']) && $_GET['suggested'] == '1';
?>

<div class="page-wrapper">

    <h1 class="page-heading">Подать обращение</h1>
    <p class="page-desc">Заполните форму. Обращение полностью анонимно — ваши данные не сохраняются.</p>

    <?php if ($success): ?>
    <div class="alert-success-custom">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            <div class="alert-title">Обращение принято</div>
            <div class="alert-text">Ваша жалоба добавлена в список обращений.</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($suggest_sent): ?>
    <div class="alert-success-custom">
        <i class="bi bi-check-circle-fill"></i>
        <div>
            <div class="alert-title">Предложение отправлено</div>
            <div class="alert-text">Администраторы рассмотрят ваш запрос и добавят учреждение при одобрении.</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger mb-4 rounded-3 border-0" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        Ошибка при отправке. Проверьте данные и попробуйте снова.
    </div>
    <?php endif; ?>

    <div class="form-card">
        <form action="scripts/save.php" method="POST" novalidate>

            <div class="mb-4">
                <label class="form-label" for="institution_id">
                    Учебное заведение
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
                <!-- Кнопка предложения -->
                <button type="button" class="suggest-trigger mt-2" data-bs-toggle="modal" data-bs-target="#suggestModal">
                    <i class="bi bi-question-circle"></i> Нет вашего учебного заведения?
                </button>
            </div>

            <div class="mb-4">
                <label class="form-label" for="message">
                    Суть проблемы
                </label>
                <textarea
                    name="message"
                    id="message"
                    class="form-control"
                    rows="5"
                    required
                    placeholder="Опишите ситуацию подробно…"
                ></textarea>
            </div>

            <hr class="opacity-10 my-4">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Отправить
                </button>
                <a href="index.php" class="btn-link-back">← К списку обращений</a>
            </div>

            <div class="anon-note mt-3">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Обращение отправляется <strong>анонимно</strong>. Личные данные не фиксируются.</span>
            </div>

        </form>
    </div>

</div>

<!-- ══ МОДАЛЬНОЕ ОКНО — предложить учреждение ══ -->
<div class="modal fade" id="suggestModal" tabindex="-1" aria-labelledby="suggestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px; overflow:hidden;">

            <div class="modal-header border-0 pb-0" style="padding: 28px 28px 0;">
                <div>
                    <div style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--accent); margin-bottom:6px;">
                        Предложение
                    </div>
                    <h5 class="modal-title" id="suggestModalLabel"
                        style="font-family:'Syne',sans-serif; font-size:20px; font-weight:800; color:var(--ink); margin:0;">
                        Добавить учреждение
                    </h5>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>

            <div class="modal-body" style="padding: 20px 28px 28px;">
                <p style="font-size:13.5px; color:var(--muted); margin-bottom:20px;">
                    Укажите полное название учебного заведения. Администраторы проверят и добавят его в список.
                </p>

                <form action="scripts/save_suggestion.php" method="POST" novalidate id="suggestForm">
                    <div class="mb-4">
                        <label class="form-label" for="suggest_title">Название учреждения</label>
                        <input
                            type="text"
                            name="title"
                            id="suggest_title"
                            class="form-control"
                            placeholder="Например: Сыктывкарский политехнический колледж"
                            required
                            maxlength="255"
                        >
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Отправить предложение
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отмена</button>
                    </div>

                    <div class="anon-note mt-3">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Предложение анонимно. Ваши данные не сохраняются.</span>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
