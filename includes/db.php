<?php
/**
 * includes/db.php — PDO singleton
 * Returns a shared PDO connection configured from config.php
 */

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST, DB_NAME, DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        if (APP_ENV === 'production') {
            http_response_code(500);
            die('Database connection failed.');
        }
        throw $e;
    }

    return $pdo;
}

/**
 * Convenience: run a prepared query and return all rows.
 */
function db_query(string $sql, array $params = []): array {
    $stmt = get_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Convenience: run a prepared query and return one row.
 */
function db_row(string $sql, array $params = []): ?array {
    $stmt = get_db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row !== false ? $row : null;
}

/**
 * Convenience: run a prepared INSERT/UPDATE/DELETE and return lastInsertId.
 */
function db_exec(string $sql, array $params = []): string {
    $stmt = get_db()->prepare($sql);
    $stmt->execute($params);
    return get_db()->lastInsertId();
}
