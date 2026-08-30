<?php
/**
 * Database Connection Configuration
 * Mergen Pharmacy Management System
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pharmacy_management');

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error to file for easier debugging and return false so helper can
            // use fallback sample data when database is not reachable.
            $logDir = __DIR__ . '/../logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/db_errors.log';
            $msg = date('Y-m-d H:i:s') . " - PDOException: " . $e->getMessage() . PHP_EOL;
            @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
            return false;
        }
    }
    return $pdo;
}
?>
