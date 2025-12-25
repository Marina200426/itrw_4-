<?php

/**
 * Endpoint для удаления комментария
 * DELETE /api/comments/delete.php?uuid=<UUID>
 */

// Отключение вывода ошибок в HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Установка заголовков до любых выводов
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка OPTIONS запроса для CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Подключение всех необходимых файлов
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Utils/UUID.php';
require_once __DIR__ . '/../../src/Repositories/CommentsRepositoryInterface.php';
require_once __DIR__ . '/../../src/Repositories/CommentsRepository.php';
require_once __DIR__ . '/../../src/Exceptions/CommentNotFoundException.php';

// Инициализация базы данных
try {
    $dbConfig = new DatabaseConfig();
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database initialization failed: ' . $e->getMessage()]);
    exit;
}

$commentsRepository = new CommentsRepository($pdo);

// Обработка только DELETE запросов
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$uuid = $_GET['uuid'] ?? null;

if (!$uuid) {
    http_response_code(400);
    echo json_encode(['error' => 'UUID parameter is required']);
    exit;
}

try {
    $commentUuid = new UUID($uuid);
    $commentsRepository->delete($commentUuid);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Comment deleted successfully'
    ]);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid UUID format']);
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode(['error' => 'Comment not found: ' . $uuid]);
}

