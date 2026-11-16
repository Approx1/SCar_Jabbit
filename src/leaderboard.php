<?php
declare(strict_types=1);

function top_rankings(PDO $pdo, int $limit): array
{
    $stmt = $pdo->prepare(
        'SELECT player_name, bio, time_ms
           FROM leaderboard
          ORDER BY time_ms ASC, id ASC
          LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll();
    foreach ($rows as $index => &$row) {
        $row['position'] = $index + 1;
        $row['time_ms']  = (int) $row['time_ms'];
    }

    return $rows;
}

function total_players(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(*) FROM leaderboard')->fetchColumn();
}

function player_record(PDO $pdo, string $playerName): ?array
{
    $stmt = $pdo->prepare(
        'SELECT a.player_name, a.bio, a.time_ms,
                (SELECT COUNT(*) + 1 FROM leaderboard b WHERE b.time_ms < a.time_ms) AS position
           FROM leaderboard a
          WHERE LOWER(a.player_name) = LOWER(:name)
          ORDER BY a.time_ms ASC
          LIMIT 1'
    );
    $stmt->execute([':name' => $playerName]);

    $row = $stmt->fetch();
    if ($row === false) {
        return null;
    }

    $row['position'] = (int) $row['position'];
    $row['time_ms']  = (int) $row['time_ms'];

    return $row;
}
