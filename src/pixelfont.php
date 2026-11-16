<?php
declare(strict_types=1);

const PIXEL_FONT_HEIGHT = 10;

function pixel_glyphs(): array
{
    return [
        'S' => ['.#####.', '#######', '##...##', '##.....', '######.', '.######', '.....##', '##...##', '#######', '.#####.'],
        'C' => ['.#####.', '#######', '##...##', '##.....', '##.....', '##.....', '##.....', '##...##', '#######', '.#####.'],
        'J' => ['..#####', '..#####', '.....##', '.....##', '.....##', '.....##', '##...##', '##...##', '#######', '.#####.'],
        'N' => ['##...##', '###..##', '###..##', '####.##', '#######', '##.####', '##..###', '##..###', '##...##', '##...##'],
        'A' => ['..###..', '.#####.', '###.###', '##...##', '##...##', '#######', '#######', '##...##', '##...##', '##...##'],
        'R' => ['######.', '#######', '##...##', '##...##', '#######', '######.', '##.###.', '##..###', '##...##', '##...##'],
        'a' => ['.......', '.......', '.......', '.#####.', '.....##', '.######', '##...##', '##...##', '#######', '.######'],
        'b' => ['##.....', '##.....', '##.....', '######.', '#######', '##...##', '##...##', '##...##', '#######', '######.'],
        'r' => ['......', '......', '......', '##.###', '######', '###...', '##....', '##....', '##....', '##....'],
        'i' => ['##', '##', '..', '##', '##', '##', '##', '##', '##', '##'],
        't' => ['.##..', '.##..', '.##..', '#####', '#####', '.##..', '.##..', '.##..', '.####', '..###'],
        '2' => ['.#####.', '#######', '##...##', '.....##', '....###', '..####.', '.####..', '###....', '#######', '#######'],
        '7' => ['#######', '#######', '.....##', '....###', '...###.', '...##..', '..###..', '..##...', '..##...', '..##...'],
        '1' => ['..##..', '.###..', '####..', '..##..', '..##..', '..##..', '..##..', '..##..', '######', '######'],
        '-' => ['.....', '.....', '.....', '.....', '#####', '#####', '.....', '.....', '.....', '.....'],
    ];
}

function pixel_text_layout(string $text, int $gap): array
{
    $glyphs = pixel_glyphs();
    $placed = [];
    $x      = 0;

    foreach (str_split($text) as $char) {
        if ($char === ' ') {
            $x += 4;
            continue;
        }
        if (!isset($glyphs[$char])) {
            continue;
        }
        $placed[] = ['x' => $x, 'rows' => $glyphs[$char]];
        $x += strlen($glyphs[$char][0]) + $gap;
    }

    return ['width' => max(0, $x - $gap), 'glyphs' => $placed];
}

function pixel_runs(array $rows): array
{
    $runs = [];
    foreach ($rows as $y => $row) {
        $length = strlen($row);
        $x = 0;
        while ($x < $length) {
            if ($row[$x] !== '#') {
                $x++;
                continue;
            }
            $start = $x;
            while ($x < $length && $row[$x] === '#') {
                $x++;
            }
            $runs[] = [$start, $y, $x - $start];
        }
    }

    return $runs;
}

function pixel_logo(string $text, ?string $glowColor = null): string
{
    $layout = pixel_text_layout($text, 2);
    $pad    = 2;
    $top    = 1;
    $shadow = 2;
    $width  = $layout['width'] + 2 * $pad;
    $height = PIXEL_FONT_HEIGHT + 2 * $pad + $top + $shadow;

    if ($glowColor !== null) {
        $svg = sprintf('<svg class="logo__art logo__art--glow" viewBox="0 0 %d %d" aria-hidden="true">', $width, $height);
        foreach ($layout['glyphs'] as $glyph) {
            foreach (pixel_runs($glyph['rows']) as [$x, $y, $length]) {
                $svg .= sprintf(
                    '<rect x="%d" y="%d" width="%d" height="5" fill="%s"/>',
                    $pad + $glyph['x'] + $x - 2,
                    $top + $pad + $y - 2,
                    $length + 4,
                    $glowColor
                );
            }
        }

        return $svg . '</svg>';
    }

    $faceRows = ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#f3efff', '#f3efff', '#e2dbff', '#e2dbff', '#cbc1ff', '#cbc1ff'];
    $layers   = [
        ['expand' => 2, 'dy' => $shadow, 'fill' => '#1f1470'],
        ['expand' => 2, 'dy' => 0,       'fill' => '#ffffff'],
        ['expand' => 1, 'dy' => 0,       'fill' => '#3f2cb4'],
        ['expand' => 0, 'dy' => 0,       'fill' => null],
    ];

    $svg = sprintf('<svg class="logo__art" viewBox="0 0 %d %d" shape-rendering="crispEdges" aria-hidden="true">', $width, $height);

    foreach ($layers as $layer) {
        $expand = $layer['expand'];
        foreach ($layout['glyphs'] as $index => $glyph) {
            $svg .= sprintf('<g class="logo__glyph" style="--i:%d">', $index);
            foreach (pixel_runs($glyph['rows']) as [$x, $y, $length]) {
                $svg .= sprintf(
                    '<rect x="%d" y="%d" width="%d" height="%d" fill="%s"/>',
                    $pad + $glyph['x'] + $x - $expand,
                    $top + $pad + $y - $expand + $layer['dy'],
                    $length + 2 * $expand,
                    1 + 2 * $expand,
                    $layer['fill'] ?? $faceRows[$y]
                );
            }
            $svg .= '</g>';
        }
    }

    return $svg . '</svg>';
}

function pixel_plate(string $text): string
{
    $layout = pixel_text_layout($text, 1);
    $padX   = 6;
    $padY   = 4;
    $width  = $layout['width'] + 2 * $padX;
    $height = PIXEL_FONT_HEIGHT + 2 * $padY;

    $svg = sprintf('<svg class="logo__plate" viewBox="0 0 %d %d" shape-rendering="crispEdges" aria-hidden="true">', $width, $height);

    foreach ([[0, '#3f2cb4'], [1, '#ffffff'], [2, '#3f2cb4']] as [$inset, $fill]) {
        $svg .= sprintf('<rect x="%d" y="%d" width="%d" height="%d" fill="%s"/>', $inset + 1, $inset, $width - 2 * $inset - 2, $height - 2 * $inset, $fill);
        $svg .= sprintf('<rect x="%d" y="%d" width="%d" height="%d" fill="%s"/>', $inset, $inset + 1, $width - 2 * $inset, $height - 2 * $inset - 2, $fill);
    }

    foreach ($layout['glyphs'] as $glyph) {
        foreach (pixel_runs($glyph['rows']) as [$x, $y, $length]) {
            $svg .= sprintf('<rect x="%d" y="%d" width="%d" height="1" fill="#ffffff"/>', $padX + $glyph['x'] + $x, $padY + $y, $length);
        }
    }

    return $svg . '</svg>';
}
