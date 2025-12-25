<?php

/**
 * Endpoint для удаления статьи
 * DELETE /api/posts/delete.php?uuid=<UUID>
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
require_once __DIR__ . '/../../src/Repositories/PostsRepositoryInterface.php';
require_once __DIR__ . '/../../src/Repositories/PostsRepository.php';
require_once __DIR__ . '/../../src/Repositories/CommentsRepositoryInterface.php';
require_once __DIR__ . '/../../src/Repositories/CommentsRepository.php';
require_once __DIR__ . '/../../src/Exceptions/PostNotFoundException.php';

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

$postsRepository = new PostsRepository($pdo);
$commentsRepository = new CommentsRepository($pdo);

// Обработка только DELETE запросов
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$uuid = $_GET['uuid'] ?? null;
$force = isset($_GET['force']) && $_GET['force'] === 'true'; // Параметр для принудительного удаления

if (!$uuid) {
    http_response_code(400);
    echo json_encode(['error' => 'UUID parameter is required']);
    exit;
}

try {
    $postUuid = new UUID($uuid);
    
    // Проверяем наличие комментариев
    $comments = $commentsRepository->getByPostUuid($postUuid);
    $commentsCount = count($comments);
    
    if ($commentsCount > 0 && !$force) {
        // Возвращаем ошибку с информацией о комментариях
        $commentsData = [];
        foreach ($comments as $comment) {
            $commentsData[] = [
                'uuid' => $comment->getUuid()->getValue(),
                'text' => $comment->getText()
            ];
        }
        
        http_response_code(409); // Conflict
        echo json_encode([
            'error' => 'Cannot delete post: it has ' . $commentsCount . ' comment(s)',
            'message' => 'Перед удалением статьи необходимо удалить все комментарии к ней.',
            'comments_count' => $commentsCount,
            'comments' => $commentsData,
            'suggestion' => 'Удалите комментарии вручную или используйте параметр force=true для автоматического удаления'
        ]);
        exit;
    }
    
    // Если есть комментарии и force=true, удаляем их автоматически
    if ($commentsCount > 0 && $force) {
        $deletedComments = $commentsRepository->deleteByPostUuid($postUuid);
        
        // Удаляем статью
        $postsRepository->delete($postUuid);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Post and ' . $deletedComments . ' comment(s) deleted successfully',
            'deleted_comments' => $deletedComments,
            'warning' => 'Было автоматически удалено ' . $deletedComments . ' комментариев вместе со статьей'
        ]);
    } else {
        // Удаляем статью (комментариев нет)
        $postsRepository->delete($postUuid);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid UUID format']);
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found: ' . $uuid]);
}

