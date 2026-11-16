SET NAMES utf8mb4;

DROP TABLE IF EXISTS leaderboard;

CREATE TABLE leaderboard (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    player_name VARCHAR(50)  NOT NULL,
    bio         VARCHAR(120) NOT NULL DEFAULT 'No bio',
    time_ms     INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_leaderboard_time (time_ms)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO leaderboard (player_name, bio, time_ms) VALUES
    ('Daemon Slayer',   'systemctl restart semangat',           67320),
    ('Kelinci Ngebut',  'Ngopi dulu, baru ngebut',              39750),
    ('Sudo Sandwich',   'sudo make me a sandwich',              85190),
    ('Subnet Samurai',  'No bio',                               47730),
    ('Kernel Panik',    'Tenang, tinggal reboot',              104880),
    ('Ping Pong Pro',   'Request timed out? Belum pernah.',     58460),
    ('Mas Localhost',   'Di laptop saya jalan kok',             93410),
    ('Bash Budi',       'rm -rf rasa malas',                   121050);
