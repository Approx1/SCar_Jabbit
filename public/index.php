<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

const BEST_RECORD_PLACEHOLDER = '--:--:--';

$players = null;
try {
    $players = total_players(db());
} catch (Throwable $exception) {
    $players = null;
}

$menu = [
    ['label' => 'Start Game', 'href' => 'start.php'],
    ['label' => 'Rankings',   'href' => 'rankings.php'],
];

render_head('Main Menu', 'page-menu');
render_window_open(APP_TITLE . ' main menu');
?>
            <div class="hud">
                <header class="logo">
                    <p class="logo__tag">
                        <span class="visually-hidden"><?= e(APP_COHORT) ?></span>
                        <?= pixel_plate(APP_COHORT) ?>
                    </p>
                    <h1 class="logo__title">
                        <span class="visually-hidden"><?= e(APP_TITLE) ?></span>
                        <?= pixel_logo(APP_TITLE, '#f4a6ff') ?>
                        <?= pixel_logo(APP_TITLE) ?>
                    </h1>
                </header>

                <nav class="menu" aria-label="Main menu">
                    <?php foreach ($menu as $index => $item): ?>
                        <a class="menu__item<?= $index === 0 ? ' is-selected' : '' ?>" href="<?= e($item['href']) ?>" style="--i:<?= $index ?>">
                            <span class="menu__bar px-corners px-corners--sm">
                                <?= sprite_arrow('menu__arrow') ?>
                                <span class="menu__label"><?= e($item['label']) ?></span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <p class="hint" aria-label="Controls: W and S to move, Space to select">
                <kbd>W</kbd><kbd>S</kbd> Move <kbd>Space</kbd> Select
            </p>

            <p class="record">
                <span class="record__label">Best Record</span>
                <span class="record__time"><?= e(BEST_RECORD_PLACEHOLDER) ?></span>
            </p>

            <div class="props" aria-hidden="true">
                <div class="props__rabbit"><?= sprite_rabbit() ?></div>
                <div class="signpost">
                    <svg class="signpost__sign" viewBox="0 0 120 110">
                        <path d="M60 8 L112 98 Q114 104 108 104 L12 104 Q6 104 8 98 Z" fill="#ffffff" stroke="#f4a6ff" stroke-width="4" stroke-linejoin="round"/>
                        <path d="M60 24 L98 92 L22 92 Z" fill="#4a36c9" stroke="#4a36c9" stroke-width="6" stroke-linejoin="round"/>
                        <text x="60" y="84" text-anchor="middle" font-size="34" font-weight="900" fill="#ffffff" font-family="Arial Black, Arial, sans-serif"><?= e($players === null ? '?' : (string) $players) ?></text>
                    </svg>
                    <span class="signpost__pole"></span>
                </div>
            </div>
<?php render_window_close(); ?>

<script>
    (function () {
        var items = Array.prototype.slice.call(document.querySelectorAll('.menu__item'));
        var selected = 0;
        var leaving = false;

        function select(index) {
            selected = (index + items.length) % items.length;
            items.forEach(function (item, i) {
                item.classList.toggle('is-selected', i === selected);
            });
        }

        function confirm(index) {
            if (leaving) {
                return;
            }
            leaving = true;
            select(index);
            items[selected].classList.add('is-confirm');
            window.setTimeout(function () {
                window.location.href = items[selected].href;
            }, 280);
        }

        document.addEventListener('keydown', function (event) {
            if (event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }
            var key = event.key.toLowerCase();
            if (key === 'w' || key === 'arrowup') {
                event.preventDefault();
                select(selected - 1);
            } else if (key === 's' || key === 'arrowdown') {
                event.preventDefault();
                select(selected + 1);
            } else if (key === ' ' || key === 'spacebar' || key === 'enter') {
                event.preventDefault();
                confirm(selected);
            }
        });

        items.forEach(function (item, index) {
            item.addEventListener('focus', function () {
                select(index);
            });
            item.addEventListener('click', function (event) {
                if (event.ctrlKey || event.metaKey || event.shiftKey || event.button !== 0) {
                    return;
                }
                event.preventDefault();
                confirm(index);
            });
        });

        window.addEventListener('pageshow', function () {
            leaving = false;
            items.forEach(function (item) {
                item.classList.remove('is-confirm');
            });
        });
    })();
</script>
<?php
render_foot();
