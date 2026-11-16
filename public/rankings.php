<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$rankings = [];
$trainee  = env('DB_USERNAME');
$mine     = null;
$error    = null;

try {
    $pdo      = db();
    $rankings = top_rankings($pdo, RANKING_LIMIT);
    if ($trainee !== null && $trainee !== '') {
        $mine = player_record($pdo, $trainee);
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

render_head('Rankings', 'page-rankings');
?>
<div class="decor" aria-hidden="true">
    <span class="decor__tile decor__tile--a"></span>
    <span class="decor__tile decor__tile--b"></span>
    <span class="decor__tile decor__tile--c"></span>
    <span class="decor__tile decor__tile--d"></span>
    <span class="decor__tile decor__tile--e"></span>
</div>

<header class="topbar">
    <div class="topbar__brand">
        <span class="topbar__icon"><?= icon('gamepad') ?></span>
        <div>
            <p class="topbar__name"><?= e(APP_TITLE) ?> @<?= e(APP_COHORT) ?></p>
            <p class="topbar__quote">"Jangan merasa aman!!!"</p>
        </div>
    </div>
    <div class="topbar__actions">
        <button class="topbar__button" type="button" id="rules-open"><?= icon('info') ?><span>Rules</span></button>
        <a class="topbar__button topbar__button--close" href="index.php" id="close" aria-label="Back to main menu" title="Back to main menu (Esc)"><?= icon('close') ?></a>
    </div>
</header>

<main class="board">
    <h1 class="visually-hidden">Rankings</h1>

    <?php if ($error !== null): ?>
        <section class="notice" role="alert">
            <h2 class="notice__title">Leaderboard belum bisa diakses</h2>
            <p>The page could not load rankings from the database. Check these, then reload:</p>
            <ul class="notice__list">
                <li class="<?= env_file_exists() ? 'is-ok' : 'is-missing' ?>">
                    <code>.env</code> <?= env_file_exists() ? 'found in the project root' : 'not found. Create it from .env.example' ?>
                </li>
                <li>The database container is running and <code>DB_HOST</code> / <code>DB_PORT</code> point to it</li>
                <li><code>DB_USERNAME</code> and <code>DB_PASSWORD</code> match the database user</li>
                <li><code>Leaderboard.sql</code> has been imported into <code>DB_DATABASE</code></li>
            </ul>
            <p class="notice__detail"><?= e($error) ?></p>
        </section>
    <?php elseif ($rankings === []): ?>
        <section class="notice">
            <h2 class="notice__title">No records yet</h2>
            <p>The <code>leaderboard</code> table is empty. Import <code>Leaderboard.sql</code> to fill it.</p>
        </section>
    <?php else: ?>
        <ol class="rank-list">
            <?php foreach ($rankings as $row): ?>
                <li class="rank-row" style="--i:<?= $row['position'] ?>">
                    <?= rank_badge($row['position']) ?>
                    <?= avatar($row['player_name']) ?>
                    <div class="rank-row__info">
                        <p class="rank-row__name"><?= e($row['player_name']) ?></p>
                        <p class="rank-row__bio"><?= icon('chat') ?><span><?= e($row['bio']) ?></span></p>
                    </div>
                    <p class="rank-row__time"><?= icon('clock') ?><span><?= e(format_time($row['time_ms'])) ?></span></p>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</main>

<?php if ($error === null && $trainee !== null && $trainee !== ''): ?>
    <footer class="mine">
        <div class="rank-row rank-row--mine">
            <?= rank_badge($mine['position'] ?? null) ?>
            <?= avatar($trainee) ?>
            <div class="rank-row__info">
                <p class="rank-row__name"><?= e($mine['player_name'] ?? $trainee) ?></p>
                <p class="rank-row__bio"><?= icon('chat') ?><span><?= e($mine['bio'] ?? 'Belum ada rekor. Nanti ya mainnya pas lulus NAR :D') ?></span></p>
            </div>
            <p class="rank-row__time"><?= icon('clock') ?><span><?= e(format_time($mine['time_ms'] ?? null)) ?></span></p>
        </div>
    </footer>
<?php endif; ?>

<p class="status">
    <?php if ($error === null): ?>
        DB: <?= e($trainee) ?>@<?= e(env('DB_HOST', 'db')) ?>/<?= e(env('DB_DATABASE')) ?>
    <?php else: ?>
        DB: not connected
    <?php endif; ?>
</p>

<dialog class="rules" id="rules">
    <h2 class="rules__title">Rules</h2>
    <ul class="rules__list">
        <li>Fastest time wins. Lower is better.</li>
        <li>The board shows the top <?= RANKING_LIMIT ?> runs, read live from the <code>leaderboard</code> table.</li>
        <li>The highlighted row at the bottom belongs to the database user set in <code>.env</code>.</li>
    </ul>
    <form method="dialog">
        <button class="button button--solid" type="submit">Got it</button>
    </form>
</dialog>

<script>
    (function () {
        var rules = document.getElementById('rules');
        document.getElementById('rules-open').addEventListener('click', function () {
            rules.showModal();
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !rules.open) {
                window.location.href = document.getElementById('close').href;
            }
        });
    })();
</script>
<?php
render_foot();
