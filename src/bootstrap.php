<?php
declare(strict_types=1);

const APP_ROOT      = __DIR__ . '/..';
const APP_COHORT    = 'NAR 27-1';
const APP_TITLE     = 'SCar Jabbit';
const RANKING_LIMIT = 5;

require __DIR__ . '/leaderboard.php';
require __DIR__ . '/pixelfont.php';
require __DIR__ . '/view.php';

function load_env(string $path): array
{
    if (!is_readable($path)) {
        return [];
    }

    $values = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        $quote = $value[0] ?? '';
        if (strlen($value) >= 2 && ($quote === '"' || $quote === "'") && str_ends_with($value, $quote)) {
            $value = substr($value, 1, -1);
        }

        $values[$key] = $value;
    }

    return $values;
}

function env(string $key, ?string $default = null): ?string
{
    static $file = null;
    $file ??= load_env(APP_ROOT . '/.env');

    if (array_key_exists($key, $file)) {
        return $file[$key];
    }

    $value = getenv($key);

    return $value === false ? $default : $value;
}

function env_file_exists(): bool
{
    return is_readable(APP_ROOT . '/.env');
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host     = env('DB_HOST', 'db');
    $port     = env('DB_PORT', '3306');
    $database = env('DB_DATABASE');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');

    if ($database === null || $username === null || $password === null) {
        throw new RuntimeException('DB_DATABASE, DB_USERNAME and DB_PASSWORD must be set in .env');
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 3,
    ]);

    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function format_time(?int $ms): string
{
    if ($ms === null) {
        return '--:--.--';
    }

    $minutes    = intdiv($ms, 60000);
    $seconds    = intdiv($ms % 60000, 1000);
    $hundredths = intdiv($ms % 1000, 10);

    return sprintf('%02d:%02d.%02d', $minutes, $seconds, $hundredths);
}
