<?php
declare(strict_types=1);

function render_head(string $pageTitle, string $bodyClass): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_TITLE) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">
    <?php
}

function render_foot(): void
{
    echo "</body>\n</html>\n";
}

function pixel_sprite(array $rows, array $palette, string $class): string
{
    $height = count($rows);
    $width  = max(array_map('strlen', $rows));

    $svg = sprintf(
        '<svg class="%s" viewBox="0 0 %d %d" shape-rendering="crispEdges" aria-hidden="true">',
        e($class),
        $width,
        $height
    );

    foreach ($rows as $y => $row) {
        $x = 0;
        $length = strlen($row);
        while ($x < $length) {
            $char = $row[$x];
            $run  = 1;
            while ($x + $run < $length && $row[$x + $run] === $char) {
                $run++;
            }
            if (isset($palette[$char])) {
                $svg .= sprintf('<rect x="%d" y="%d" width="%d" height="1" fill="%s"/>', $x, $y, $run, $palette[$char]);
            }
            $x += $run;
        }
    }

    return $svg . '</svg>';
}

function sprite_rabbit(string $class = 'sprite sprite--rabbit'): string
{
    $rows = [
        '..KK....KK..',
        '.KWWK..KWWK.',
        '.KWPK..KWPK.',
        '.KWPK..KWPK.',
        '.KWPK..KWPK.',
        '.KWWKKKKWWK.',
        'KWWWWWWWWWWK',
        'KWWWWWWWWWWK',
        'KWWVWWWWVWWK',
        'KWWVWWWWVWWK',
        'KWPWWKKWWPWK',
        'KWWWWWWWWWWK',
        '.KWWWWWWWWK.',
        '.KWWKKKKWWK.',
        '..KK....KK..',
    ];

    return pixel_sprite($rows, ['K' => '#2b1f7a', 'W' => '#ffffff', 'P' => '#ff9fe2', 'V' => '#5a45d6'], $class);
}

function sprite_cone(string $class = 'sprite sprite--cone'): string
{
    $rows = [
        '....KK....',
        '...KOOK...',
        '...KOOK...',
        '..KOOOOK..',
        '..KWWWWK..',
        '.KOOOOOOK.',
        '.KOOOOOOK.',
        '.KWWWWWWK.',
        'KOOOOOOOOK',
        'KKKKKKKKKK',
        'KYYYYYYYYK',
        'KKKKKKKKKK',
    ];

    return pixel_sprite($rows, ['K' => '#2b1f7a', 'O' => '#ff8a3d', 'W' => '#ffffff', 'Y' => '#ffcf4a'], $class);
}

function sprite_cloud(string $class): string
{
    $rows = [
        '......WWWW........',
        '....WWWWWWWW......',
        '..WWWWWWWWWWWW....',
        '.WWWWWWWWWWWWWWWW.',
        'WWWWWWWWWWWWWWWWWW',
        '.SSSSSSSSSSSSSSSS.',
    ];

    return pixel_sprite($rows, ['W' => '#ffffff', 'S' => '#d6e6ff'], $class);
}

function skyline_svg(): string
{
    $layers = [
        ['fill' => '#a9cdf8', 'window' => null, 'buildings' => [
            [0, 60, 62], [70, 36, 96], [118, 70, 54], [200, 44, 110], [256, 80, 70],
            [348, 40, 124], [400, 90, 60], [504, 46, 100], [560, 76, 74], [646, 98, 58],
        ]],
        ['fill' => '#6f97ea', 'window' => '#b9d6ff', 'buildings' => [
            [-10, 92, 70], [96, 50, 44], [160, 64, 86], [290, 74, 52], [372, 54, 78],
            [440, 60, 40], [520, 84, 64], [620, 50, 92], [680, 70, 50],
        ]],
        ['fill' => '#4a52d4', 'window' => '#8fb4ff', 'buildings' => [
            [0, 70, 46], [84, 44, 30], [228, 56, 34], [300, 30, 50], [352, 80, 30],
            [436, 58, 68], [512, 40, 26], [600, 44, 40], [690, 60, 34],
        ]],
    ];

    $svg = '<svg class="scene__tile" viewBox="0 0 744 150" shape-rendering="crispEdges" aria-hidden="true">';

    foreach ($layers as $layer) {
        foreach ($layer['buildings'] as [$x, $width, $height]) {
            $top  = 150 - $height;
            $svg .= sprintf('<rect x="%d" y="%d" width="%d" height="%d" fill="%s"/>', $x, $top, $width, $height, $layer['fill']);
            $svg .= sprintf('<rect x="%d" y="%d" width="%d" height="4" fill="#ffffff" opacity="0.28"/>', $x, $top, $width);

            if ($layer['window'] === null) {
                continue;
            }
            for ($wy = $top + 12; $wy < 142; $wy += 14) {
                for ($wx = $x + 8; $wx < $x + $width - 10; $wx += 14) {
                    if ((($wx * 7 + $wy * 13) % 5) < 2) {
                        $svg .= sprintf('<rect x="%d" y="%d" width="6" height="6" fill="%s"/>', $wx, $wy, $layer['window']);
                    }
                }
            }
        }
    }

    return $svg . '</svg>';
}

function sprite_arrow(string $class): string
{
    $rows = ['#...', '##..', '###.', '####', '###.', '##..', '#...'];

    return pixel_sprite($rows, ['#' => '#ffffff'], $class);
}

function sprite_spark(string $style): string
{
    $svg = pixel_sprite(['.#.', '###', '.#.'], ['#' => '#ffffff'], 'scene__spark');

    return str_replace('<svg ', '<svg style="' . e($style) . '" ', $svg);
}

function render_scene(): void
{
    $clouds = [
        [20, '7%', 0.55, '95s', '-18s'],
        [30, '2%', 0.45, '130s', '-88s'],
        [16, '40%', 0.8, '75s', '-9s'],
        [12, '24%', 0.6, '110s', '-55s'],
    ];
    $sparks = [
        ['12%', '18%', '0s'], ['27%', '9%', '1.3s'], ['43%', '30%', '0.6s'], ['61%', '12%', '2.1s'],
        ['78%', '26%', '0.9s'], ['90%', '8%', '1.7s'], ['8%', '38%', '2.6s'], ['70%', '42%', '0.3s'],
    ];
    $tile = skyline_svg();
    ?>
    <div class="scene" aria-hidden="true">
        <?php foreach ($clouds as $index => [$width, $top, $opacity, $duration, $delay]): ?>
            <?= str_replace(
                '<svg ',
                sprintf('<svg style="--w:%d;--top:%s;--o:%s;--dur:%s;--delay:%s;--rest:%d%%" ', $width, $top, $opacity, $duration, $delay, 6 + $index * 26),
                sprite_cloud('scene__cloud')
            ) ?>
        <?php endforeach; ?>
        <?php foreach ($sparks as [$left, $top, $delay]): ?>
            <?= sprite_spark("left:$left;top:$top;animation-delay:$delay") ?>
        <?php endforeach; ?>
        <div class="scene__skyline"><?= $tile . $tile . $tile ?></div>
        <div class="scene__ground"></div>
    </div>
    <div class="battery" aria-hidden="true"><i></i><i></i><i></i></div>
    <?php
}

function render_window_open(string $label): void
{
    ?>
<main class="stage">
    <section class="window" aria-label="<?= e($label) ?>">
        <div class="window__screen">
            <?php render_scene(); ?>
    <?php
}

function render_window_close(): void
{
    $rows = [
        'DDDDLL',
        'DDLLL.',
        'DLL...',
        'DL....',
        'LL....',
        'L.....',
    ];
    $palette = ['D' => '#1b1650', 'L' => '#cfc8ff'];

    echo "        </div>\n";
    foreach (['tl', 'tr', 'bl', 'br'] as $corner) {
        echo '        ' . pixel_sprite($rows, $palette, 'window__corner window__corner--' . $corner) . "\n";
    }
    echo "    </section>\n</main>\n";
}

function icon(string $name): string
{
    $paths = [
        'clock'   => '<circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2.4"/>'
                   . '<path d="M12 7v5.5l3.5 2" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>',
        'chat'    => '<path d="M4 5h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-7l-5 4v-4H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" fill="currentColor"/>'
                   . '<circle cx="8" cy="11" r="1.3" fill="#fff"/><circle cx="12" cy="11" r="1.3" fill="#fff"/><circle cx="16" cy="11" r="1.3" fill="#fff"/>',
        'info'    => '<circle cx="12" cy="12" r="10" fill="currentColor"/>'
                   . '<rect x="10.8" y="10" width="2.4" height="7.5" rx="1" fill="#5a45d6"/><circle cx="12" cy="7" r="1.5" fill="#5a45d6"/>',
        'close'   => '<path d="M5 5l14 14M19 5L5 19" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>',
        'gamepad' => '<path d="M6 7h12a5 5 0 0 1 5 5v2a4 4 0 0 1-7.2 2.4L15 15.5H9l-.8.9A4 4 0 0 1 1 14v-2a5 5 0 0 1 5-5z" fill="currentColor"/>'
                   . '<path d="M7 10v4M5 12h4" stroke="#5a45d6" stroke-width="1.8" stroke-linecap="round"/>'
                   . '<circle cx="16" cy="11" r="1.2" fill="#5a45d6"/><circle cx="18.5" cy="13.2" r="1.2" fill="#5a45d6"/>',
        'target'  => '<circle cx="12" cy="12" r="7" fill="none" stroke="currentColor" stroke-width="2.4"/>'
                   . '<circle cx="12" cy="12" r="2" fill="currentColor"/>'
                   . '<path d="M12 2v4M12 18v4M2 12h4M18 12h4" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>',
    ];

    return '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

function rank_badge(?int $position): string
{
    $label = $position === null ? '–' : (string) $position;

    if ($position !== null && $position <= 3) {
        $tones = [1 => ['#ffc83d', '#c9820a'], 2 => ['#b9c6dc', '#6d7d9c'], 3 => ['#e79a5b', '#a4541d']];
        [$fill, $shade] = $tones[$position];

        return sprintf(
            '<svg class="rank-badge" viewBox="0 0 48 48" role="img" aria-label="Rank %1$s">'
            . '<path d="M9 12H4v5a8 8 0 0 0 8 8M39 12h5v5a8 8 0 0 1-8 8" fill="none" stroke="%3$s" stroke-width="4"/>'
            . '<path d="M11 6h26v14a13 13 0 0 1-26 0z" fill="%2$s" stroke="%3$s" stroke-width="3"/>'
            . '<rect x="20" y="32" width="8" height="6" fill="%3$s"/>'
            . '<rect x="13" y="38" width="22" height="6" rx="2" fill="%2$s" stroke="%3$s" stroke-width="3"/>'
            . '<text x="24" y="25" text-anchor="middle" font-size="17" font-weight="900" fill="#2b1f7a" font-family="Arial, sans-serif">%1$s</text>'
            . '</svg>',
            e($label),
            $fill,
            $shade
        );
    }

    return sprintf(
        '<svg class="rank-badge" viewBox="0 0 48 48" role="img" aria-label="Rank %1$s">'
        . '<rect x="9" y="9" width="30" height="30" rx="7" fill="#f4f1ff" stroke="#8f84e6" stroke-width="3"/>'
        . '<text x="24" y="30" text-anchor="middle" font-size="18" font-weight="900" fill="#2b1f7a" font-family="Arial, sans-serif">%1$s</text>'
        . '</svg>',
        e($label)
    );
}

function avatar(string $playerName): string
{
    $hue = crc32(strtolower($playerName)) % 360;

    return sprintf(
        '<svg class="avatar" viewBox="0 0 48 48" aria-hidden="true">'
        . '<circle cx="24" cy="24" r="22" fill="hsl(%1$d 55%% 78%%)" stroke="#2b1f7a" stroke-width="2.5"/>'
        . '<circle cx="24" cy="19" r="8.5" fill="hsl(%1$d 40%% 38%%)"/>'
        . '<path d="M8.5 39a16 13 0 0 1 31 0A21 21 0 0 1 8.5 39z" fill="hsl(%1$d 40%% 38%%)"/>'
        . '</svg>',
        $hue
    );
}
