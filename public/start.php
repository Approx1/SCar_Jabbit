<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

render_head('Start Game', 'page-construction');
render_window_open('Start Game');
?>
            <div class="hud hud--construction">
                <div class="panel-wrap">
                    <div class="panel__shadow px-corners" aria-hidden="true"></div>
                    <article class="panel px-corners">
                        <div class="panel__inner px-corners">
                            <header class="panel__header">
                                <span class="panel__icon"><?= icon('target') ?></span>
                                <span class="panel__title">Start Game</span>
                                <span class="panel__note"><?= e(APP_COHORT) ?> &middot; <?= e(APP_TITLE) ?></span>
                            </header>

                            <div class="panel__body">
                                <div class="hazard" aria-hidden="true"></div>

                                <div class="construction">
                                    <?= sprite_cone('sprite sprite--cone sprite--bob') ?>
                                    <div class="construction__text">
                                        <h1 class="construction__title">Under Construction<span class="dots"><i>.</i><i>.</i><i>.</i></span></h1>
                                        <p class="construction__desc">Nanti ya mainnya pas lulus NAR :D</p>
                                    </div>
                                    <?= sprite_cone('sprite sprite--cone sprite--bob sprite--late') ?>
                                </div>

                                <div class="hazard hazard--reverse" aria-hidden="true"></div>
                            </div>
                        </div>
                    </article>
                </div>

                <a class="pixel-button px-corners px-corners--sm" href="index.php" id="back">
                    <span class="pixel-button__face px-corners px-corners--sm">&#9664; Back to Menu <kbd>Esc</kbd></span>
                </a>
            </div>
<?php render_window_close(); ?>

<script>
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            window.location.href = document.getElementById('back').href;
        }
    });
</script>
<?php
render_foot();
