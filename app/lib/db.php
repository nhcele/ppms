<?php
declare(strict_types=1);

// app/lib/db.php - PDO connection helper
require_once __DIR__ . '/../config.php';

function db(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            $detail = (defined('APP_ENV') && APP_ENV === 'dev') ? (': ' . $e->getMessage()) : '';
            throw new Exception('Database connection failed' . $detail);
        }
    }
    
    return $pdo;
}
