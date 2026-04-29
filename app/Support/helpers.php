<?php

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);
        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('resource_path')) {
    function resource_path(string $path = ''): string
    {
        return base_path('resources' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('view_path')) {
    function view_path(string $path = ''): string
    {
        return resource_path('views' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $config = [];

        $segments = explode('.', $key);
        $file = array_shift($segments);

        if (!isset($config[$file])) {
            $configFile = config_path($file . '.php');
            $config[$file] = file_exists($configFile) ? require $configFile : [];
        }

        $value = $config[$file];

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('cq_safe_identifier')) {
    function cq_safe_identifier(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            throw new InvalidArgumentException('Unsafe SQL identifier.');
        }

        return $identifier;
    }
}

if (!function_exists('cq_table_exists')) {
    function cq_table_exists(PDO $pdo, string $table): bool
    {
        static $cache = [];
        $key = $table;

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
            $stmt->execute([$table]);
            $cache[$key] = (bool) $stmt->fetchColumn();
        } catch (Throwable) {
            $cache[$key] = false;
        }

        return $cache[$key];
    }
}

if (!function_exists('cq_column_exists')) {
    function cq_column_exists(PDO $pdo, string $table, string $column): bool
    {
        static $cache = [];
        $key = $table . '.' . $column;

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $safeTable = cq_safe_identifier($table);
            $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$safeTable}` LIKE ?");
            $stmt->execute([$column]);
            $cache[$key] = (bool) $stmt->fetchColumn();
        } catch (Throwable) {
            $cache[$key] = false;
        }

        return $cache[$key];
    }
}

if (!function_exists('cq_resolve_table_name')) {
    function cq_resolve_table_name(PDO $pdo, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (cq_table_exists($pdo, $candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

if (!function_exists('cq_user_types_table')) {
    function cq_user_types_table(PDO $pdo): ?string
    {
        return cq_resolve_table_name($pdo, ['user_types', 'user_type']);
    }
}

if (!function_exists('cq_languages_table')) {
    function cq_languages_table(PDO $pdo): ?string
    {
        return cq_resolve_table_name($pdo, ['programming_languages', 'programming_language']);
    }
}

if (!function_exists('cq_quiz_types_table')) {
    function cq_quiz_types_table(PDO $pdo): ?string
    {
        return cq_resolve_table_name($pdo, ['quiz_types', 'quiz_type']);
    }
}

if (!function_exists('cq_difficulty_levels_table')) {
    function cq_difficulty_levels_table(PDO $pdo): ?string
    {
        return cq_resolve_table_name($pdo, ['difficulty_levels', 'difficulty_level']);
    }
}

if (!function_exists('cq_user_masteries_table')) {
    function cq_user_masteries_table(PDO $pdo): ?string
    {
        return cq_resolve_table_name($pdo, ['user_masteries', 'user_mastery']);
    }
}

if (!function_exists('cq_user_mastery_xp_column')) {
    function cq_user_mastery_xp_column(PDO $pdo, string $table): ?string
    {
        if (cq_column_exists($pdo, $table, 'language_xp')) {
            return 'language_xp';
        }

        if (cq_column_exists($pdo, $table, 'current_xp')) {
            return 'current_xp';
        }

        return null;
    }
}

if (!function_exists('cq_get_languages')) {
    function cq_get_languages(PDO $pdo): array
    {
        $table = cq_languages_table($pdo);
        if ($table === null) {
            return [];
        }

        $safeTable = cq_safe_identifier($table);
        $stmt = $pdo->query("SELECT id, name FROM `{$safeTable}` ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('cq_get_quiz_types')) {
    function cq_get_quiz_types(PDO $pdo): array
    {
        $table = cq_quiz_types_table($pdo);
        if ($table === null) {
            return [];
        }

        $safeTable = cq_safe_identifier($table);
        $stmt = $pdo->query("SELECT id, name FROM `{$safeTable}` ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('cq_get_difficulty_levels')) {
    function cq_get_difficulty_levels(PDO $pdo): array
    {
        $table = cq_difficulty_levels_table($pdo);
        if ($table === null) {
            return [];
        }

        $safeTable = cq_safe_identifier($table);
        $stmt = $pdo->query("SELECT id, name FROM `{$safeTable}` ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('cq_get_user_types')) {
    function cq_get_user_types(PDO $pdo): array
    {
        $table = cq_user_types_table($pdo);
        if ($table === null) {
            return [];
        }

        $safeTable = cq_safe_identifier($table);
        $stmt = $pdo->query("SELECT id, name FROM `{$safeTable}` ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('cq_get_user_type_id_by_name')) {
    function cq_get_user_type_id_by_name(PDO $pdo, array $candidateNames): ?int
    {
        $table = cq_user_types_table($pdo);
        if ($table === null || $candidateNames === []) {
            return null;
        }

        $safeTable = cq_safe_identifier($table);
        $normalizedNames = array_values(array_filter(array_map(
            static fn($name) => strtolower(trim((string) $name)),
            $candidateNames
        )));

        if ($normalizedNames === []) {
            return null;
        }

        $placeholders = implode(', ', array_fill(0, count($normalizedNames), '?'));
        $stmt = $pdo->prepare("SELECT id, name FROM `{$safeTable}` WHERE LOWER(name) IN ({$placeholders}) LIMIT 1");
        $stmt->execute($normalizedNames);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int) $row['id'] : null;
    }
}

if (!function_exists('cq_get_admin_type_id')) {
    function cq_get_admin_type_id(PDO $pdo): ?int
    {
        return cq_get_user_type_id_by_name($pdo, ['admin', 'administrator', 'overseer']);
    }
}

if (!function_exists('cq_get_default_user_type_id')) {
    function cq_get_default_user_type_id(PDO $pdo): ?int
    {
        $matchedId = cq_get_user_type_id_by_name($pdo, ['user', 'hero', 'adventurer', 'member', 'player']);
        if ($matchedId !== null) {
            return $matchedId;
        }

        $adminTypeId = cq_get_admin_type_id($pdo);
        $types = cq_get_user_types($pdo);

        foreach ($types as $type) {
            $id = (int) ($type['id'] ?? 0);
            if ($id > 0 && $id !== $adminTypeId) {
                return $id;
            }
        }

        return null;
    }
}

if (!function_exists('cq_ensure_default_user_type_id')) {
    function cq_ensure_default_user_type_id(PDO $pdo): ?int
    {
        $defaultTypeId = cq_get_default_user_type_id($pdo);
        if ($defaultTypeId !== null) {
            return $defaultTypeId;
        }

        $table = cq_user_types_table($pdo);
        if ($table === null) {
            return null;
        }

        $safeTable = cq_safe_identifier($table);

        try {
            $stmt = $pdo->prepare("INSERT INTO `{$safeTable}` (name) VALUES (?)");
            $stmt->execute(['Hero']);
            return (int) $pdo->lastInsertId();
        } catch (Throwable) {
            return cq_get_default_user_type_id($pdo);
        }
    }
}

if (!function_exists('cq_get_user_role_name')) {
    function cq_get_user_role_name(PDO $pdo, int $typeId): string
    {
        $types = cq_get_user_types($pdo);
        foreach ($types as $type) {
            if ((int) ($type['id'] ?? 0) === $typeId) {
                return (string) ($type['name'] ?? 'user');
            }
        }

        return $typeId === 1 ? 'admin' : 'user';
    }
}

if (!function_exists('cq_get_session_role_for_type_id')) {
    function cq_get_session_role_for_type_id(PDO $pdo, int $typeId): string
    {
        $roleName = strtolower(cq_get_user_role_name($pdo, $typeId));
        return in_array($roleName, ['admin', 'administrator', 'overseer'], true) ? 'admin' : 'user';
    }
}

if (!function_exists('cq_get_public_user_filter')) {
    function cq_get_public_user_filter(PDO $pdo, string $alias = 'u'): array
    {
        $safeAlias = cq_safe_identifier($alias);
        $defaultUserTypeId = cq_get_default_user_type_id($pdo);
        if ($defaultUserTypeId !== null) {
            return [
                'sql' => "{$safeAlias}.type_id = :public_user_type_id",
                'params' => [':public_user_type_id' => $defaultUserTypeId],
            ];
        }

        $adminTypeId = cq_get_admin_type_id($pdo);
        if ($adminTypeId !== null) {
            return [
                'sql' => "{$safeAlias}.type_id <> :admin_type_id",
                'params' => [':admin_type_id' => $adminTypeId],
            ];
        }

        return [
            'sql' => "{$safeAlias}.type_id > 0",
            'params' => [],
        ];
    }
}

if (!function_exists('cq_get_user_mastery_rows')) {
    function cq_get_user_mastery_rows(PDO $pdo, int $userId): array
    {
        $masteriesTable = cq_user_masteries_table($pdo);
        $languagesTable = cq_languages_table($pdo);

        if ($masteriesTable !== null) {
            $safeMasteriesTable = cq_safe_identifier($masteriesTable);
            $safeLanguagesTable = $languagesTable !== null ? cq_safe_identifier($languagesTable) : null;

            if (cq_column_exists($pdo, $masteriesTable, 'language') && cq_column_exists($pdo, $masteriesTable, 'language_xp')) {
                $stmt = $pdo->prepare("
                    SELECT
                        language AS lang,
                        language_xp AS current_xp,
                        FLOOR(language_xp / 1000) + 1 AS level
                    FROM `{$safeMasteriesTable}`
                    WHERE user_id = ?
                ");
                $stmt->execute([$userId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $xpColumn = cq_user_mastery_xp_column($pdo, $masteriesTable);
            if ($safeLanguagesTable !== null && cq_column_exists($pdo, $masteriesTable, 'language_id') && $xpColumn !== null) {
                $safeXpColumn = cq_safe_identifier($xpColumn);
                $levelExpression = cq_column_exists($pdo, $masteriesTable, 'mastery_level')
                    ? "COALESCE(um.mastery_level, FLOOR(um.{$safeXpColumn} / 1000) + 1)"
                    : "FLOOR(um.{$safeXpColumn} / 1000) + 1";
                $stmt = $pdo->prepare("
                    SELECT
                        pl.name AS lang,
                        um.{$safeXpColumn} AS current_xp,
                        {$levelExpression} AS level
                    FROM `{$safeMasteriesTable}` um
                    JOIN `{$safeLanguagesTable}` pl ON um.language_id = pl.id
                    WHERE um.user_id = ?
                ");
                $stmt->execute([$userId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        if (cq_table_exists($pdo, 'user_stats')) {
            $languagesTable = cq_languages_table($pdo);
            if ($languagesTable === null) {
                return [];
            }
            $safeLanguagesTable = cq_safe_identifier($languagesTable);
            $stmt = $pdo->prepare("
                SELECT
                    pl.name AS lang,
                    us.current_xp AS current_xp,
                    FLOOR(us.current_xp / 1000) + 1 AS level
                FROM user_stats us
                JOIN `{$safeLanguagesTable}` pl ON us.language_id = pl.id
                WHERE us.user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (cq_table_exists($pdo, 'leaderboards')) {
            $languagesTable = cq_languages_table($pdo);
            if ($languagesTable === null) {
                return [];
            }
            $safeLanguagesTable = cq_safe_identifier($languagesTable);
            $stmt = $pdo->prepare("
                SELECT
                    pl.name AS lang,
                    MAX(l.score) AS current_xp,
                    FLOOR(MAX(l.score) / 1000) + 1 AS level
                FROM leaderboards l
                JOIN `{$safeLanguagesTable}` pl ON l.language_id = pl.id
                WHERE l.user_id = ?
                GROUP BY pl.name
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }
}

if (!function_exists('cq_get_language_leaderboard_rows')) {
    function cq_get_language_leaderboard_rows(PDO $pdo, string $lang, int $limit = 50): array
    {
        $titleExpression = cq_column_exists($pdo, 'users', 'title')
            ? 'u.title'
            : "'" . addslashes('Novice Scripter') . "'";
        $masteriesTable = cq_user_masteries_table($pdo);
        $languagesTable = cq_languages_table($pdo);
        $publicUserFilter = cq_get_public_user_filter($pdo, 'u');

        if ($masteriesTable !== null && cq_column_exists($pdo, $masteriesTable, 'language') && cq_column_exists($pdo, $masteriesTable, 'language_xp')) {
            $safeMasteriesTable = cq_safe_identifier($masteriesTable);
            $stmt = $pdo->prepare("
                SELECT u.username, {$titleExpression} AS rank_title, um.language_xp AS score
                FROM users u
                JOIN `{$safeMasteriesTable}` um ON u.id = um.user_id
                WHERE um.language = :lang
                  AND {$publicUserFilter['sql']}
                ORDER BY um.language_xp DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':lang', $lang);
            foreach ($publicUserFilter['params'] as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($masteriesTable !== null && $languagesTable !== null && cq_column_exists($pdo, $masteriesTable, 'language_id')) {
            $safeMasteriesTable = cq_safe_identifier($masteriesTable);
            $safeLanguagesTable = cq_safe_identifier($languagesTable);
            $xpColumn = cq_user_mastery_xp_column($pdo, $masteriesTable);

            if ($xpColumn !== null) {
                $safeXpColumn = cq_safe_identifier($xpColumn);
                $stmt = $pdo->prepare("
                    SELECT u.username, {$titleExpression} AS rank_title, um.{$safeXpColumn} AS score
                    FROM users u
                    JOIN `{$safeMasteriesTable}` um ON u.id = um.user_id
                    JOIN `{$safeLanguagesTable}` pl ON um.language_id = pl.id
                    WHERE pl.name = :lang
                      AND {$publicUserFilter['sql']}
                    ORDER BY um.{$safeXpColumn} DESC, u.id ASC
                    LIMIT :limit
                ");
                $stmt->bindValue(':lang', $lang);
                foreach ($publicUserFilter['params'] as $key => $value) {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                }
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        if (cq_table_exists($pdo, 'user_stats') && $languagesTable !== null) {
            $safeLanguagesTable = cq_safe_identifier($languagesTable);
            $stmt = $pdo->prepare("
                SELECT u.username, {$titleExpression} AS rank_title, us.current_xp AS score
                FROM users u
                JOIN user_stats us ON u.id = us.user_id
                JOIN `{$safeLanguagesTable}` pl ON us.language_id = pl.id
                WHERE pl.name = :lang
                  AND {$publicUserFilter['sql']}
                ORDER BY us.current_xp DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':lang', $lang);
            foreach ($publicUserFilter['params'] as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($languagesTable === null) {
            return [];
        }

        $safeLanguagesTable = cq_safe_identifier($languagesTable);
        $stmt = $pdo->prepare("
            SELECT u.username, {$titleExpression} AS rank_title, l.score AS score
            FROM leaderboards l
            JOIN users u ON l.user_id = u.id
            JOIN `{$safeLanguagesTable}` pl ON l.language_id = pl.id
            WHERE pl.name = :lang
              AND {$publicUserFilter['sql']}
            ORDER BY l.score DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':lang', $lang);
        foreach ($publicUserFilter['params'] as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('cq_get_social_feed_rows')) {
    function cq_get_social_feed_rows(PDO $pdo, string $lang, int $limit, int $offset): array
    {
        $masteriesTable = cq_user_masteries_table($pdo);
        $languagesTable = cq_languages_table($pdo);
        $publicUserFilter = cq_get_public_user_filter($pdo, 'u');

        if ($masteriesTable !== null && cq_column_exists($pdo, $masteriesTable, 'language') && cq_column_exists($pdo, $masteriesTable, 'language_xp')) {
            $safeMasteriesTable = cq_safe_identifier($masteriesTable);
            $query = "
                SELECT
                    u.username,
                    um.language AS language,
                    um.language_xp AS score,
                    " . (cq_column_exists($pdo, $masteriesTable, 'updated_at') ? "um.updated_at" : "NOW()") . " AS last_updated
                FROM `{$safeMasteriesTable}` um
                JOIN users u ON um.user_id = u.id
            ";

            $conditions = [$publicUserFilter['sql']];

            if ($lang !== 'ALL') {
                $conditions[] = "um.language = :lang";
            }

            $query .= " WHERE " . implode(' AND ', $conditions);
            $query .= " ORDER BY last_updated DESC LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($query);
            if ($lang !== 'ALL') {
                $stmt->bindValue(':lang', $lang);
            }
            foreach ($publicUserFilter['params'] as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($masteriesTable !== null && $languagesTable !== null && cq_column_exists($pdo, $masteriesTable, 'language_id')) {
            $safeMasteriesTable = cq_safe_identifier($masteriesTable);
            $safeLanguagesTable = cq_safe_identifier($languagesTable);
            $xpColumn = cq_user_mastery_xp_column($pdo, $masteriesTable);
            $updatedColumn = cq_column_exists($pdo, $masteriesTable, 'updated_at') ? 'um.updated_at' : 'NOW()';

            if ($xpColumn !== null) {
                $safeXpColumn = cq_safe_identifier($xpColumn);
                $query = "
                    SELECT
                        u.username,
                        pl.name AS language,
                        um.{$safeXpColumn} AS score,
                        {$updatedColumn} AS last_updated
                    FROM `{$safeMasteriesTable}` um
                    JOIN users u ON um.user_id = u.id
                    JOIN `{$safeLanguagesTable}` pl ON um.language_id = pl.id
                ";

                $conditions = [$publicUserFilter['sql']];

                if ($lang !== 'ALL') {
                    $conditions[] = "pl.name = :lang";
                }

                $query .= " WHERE " . implode(' AND ', $conditions);
                $query .= " ORDER BY last_updated DESC LIMIT :limit OFFSET :offset";
                $stmt = $pdo->prepare($query);
                if ($lang !== 'ALL') {
                    $stmt->bindValue(':lang', $lang);
                }
                foreach ($publicUserFilter['params'] as $key => $value) {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                }
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        if (cq_table_exists($pdo, 'user_stats') && $languagesTable !== null) {
            $safeLanguagesTable = cq_safe_identifier($languagesTable);
            $query = "
                SELECT
                    u.username,
                    pl.name AS language,
                    us.current_xp AS score,
                    us.last_activity AS last_updated
                FROM user_stats us
                JOIN users u ON us.user_id = u.id
                JOIN `{$safeLanguagesTable}` pl ON us.language_id = pl.id
            ";

            $conditions = [$publicUserFilter['sql']];

            if ($lang !== 'ALL') {
                $conditions[] = "pl.name = :lang";
            }

            $query .= " WHERE " . implode(' AND ', $conditions);
            $query .= " ORDER BY us.last_activity DESC LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($query);
            if ($lang !== 'ALL') {
                $stmt->bindValue(':lang', $lang);
            }
            foreach ($publicUserFilter['params'] as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($languagesTable === null) {
            return [];
        }

        $safeLanguagesTable = cq_safe_identifier($languagesTable);
        $query = "
            SELECT
                u.username,
                pl.name AS language,
                l.score AS score,
                l.last_updated AS last_updated
            FROM leaderboards l
            JOIN users u ON l.user_id = u.id
            JOIN `{$safeLanguagesTable}` pl ON l.language_id = pl.id
        ";

        $conditions = [$publicUserFilter['sql']];

        if ($lang !== 'ALL') {
            $conditions[] = "pl.name = :lang";
        }

        $query .= " WHERE " . implode(' AND ', $conditions);
        $query .= " ORDER BY l.last_updated DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($query);
        if ($lang !== 'ALL') {
            $stmt->bindValue(':lang', $lang);
        }
        foreach ($publicUserFilter['params'] as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('cq_get_user_total_xp')) {
    function cq_get_user_total_xp(PDO $pdo, int $userId): int
    {
        if (cq_column_exists($pdo, 'users', 'xp')) {
            $stmt = $pdo->prepare("SELECT COALESCE(xp, 0) FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return (int) ($stmt->fetchColumn() ?: 0);
        }

        if (cq_table_exists($pdo, 'user_stats')) {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(current_xp), 0) FROM user_stats WHERE user_id = ?");
            $stmt->execute([$userId]);
            return (int) ($stmt->fetchColumn() ?: 0);
        }

        if (cq_table_exists($pdo, 'leaderboards')) {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(score), 0) FROM leaderboards WHERE user_id = ?");
            $stmt->execute([$userId]);
            return (int) ($stmt->fetchColumn() ?: 0);
        }

        return 0;
    }
}

if (!function_exists('cq_get_user_title')) {
    function cq_get_user_title(PDO $pdo, int $userId, string $fallback = 'Novice Scripter'): string
    {
        if (cq_column_exists($pdo, 'users', 'title')) {
            $stmt = $pdo->prepare("SELECT title FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $title = $stmt->fetchColumn();

            if (is_string($title) && trim($title) !== '') {
                return $title;
            }
        }

        return $fallback;
    }
}

if (!function_exists('cq_get_global_leaderboard_rows')) {
    function cq_get_global_leaderboard_rows(PDO $pdo, ?int $limit = 50): array
    {
        $limitSql = $limit !== null ? ' LIMIT :limit' : '';
        $titleExpression = cq_column_exists($pdo, 'users', 'title')
            ? 'u.title'
            : "'" . addslashes('Novice Scripter') . "'";
        $publicUserFilter = cq_get_public_user_filter($pdo, 'u');

        if (cq_column_exists($pdo, 'users', 'xp')) {
            $query = "
                SELECT
                    u.id,
                    u.username,
                    {$titleExpression} AS rank_title,
                    COALESCE(u.xp, 0) AS score
                FROM users u
                WHERE {$publicUserFilter['sql']}
                ORDER BY score DESC, u.id ASC" . $limitSql;
            $stmt = $pdo->prepare($query);
        } elseif (cq_table_exists($pdo, 'user_stats')) {
            $query = "
                SELECT
                    u.id,
                    u.username,
                    {$titleExpression} AS rank_title,
                    COALESCE(SUM(us.current_xp), 0) AS score
                FROM users u
                LEFT JOIN user_stats us ON u.id = us.user_id
                WHERE {$publicUserFilter['sql']}
                GROUP BY u.id, u.username
                ORDER BY score DESC, u.id ASC" . $limitSql;
            $stmt = $pdo->prepare($query);
        } else {
            $query = "
                SELECT
                    u.id,
                    u.username,
                    {$titleExpression} AS rank_title,
                    COALESCE(SUM(l.score), 0) AS score
                FROM users u
                LEFT JOIN leaderboards l ON u.id = l.user_id
                WHERE {$publicUserFilter['sql']}
                GROUP BY u.id, u.username
                ORDER BY score DESC, u.id ASC" . $limitSql;
            $stmt = $pdo->prepare($query);
        }

        foreach ($publicUserFilter['params'] as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
