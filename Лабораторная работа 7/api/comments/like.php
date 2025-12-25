<?php

/**
 * Endpoint для добавления лайка к комментарию
 * POST /api/comments/like.php
 */

// Включение буферизации вывода для предотвращения случайного вывода
ob_start();

// Отключение вывода ошибок в HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Установка заголовков до любых выводов
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработчик фатальных ошибок
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            'error' => 'Fatal error: ' . $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        exit;
    }
});

// Обработка OPTIONS запроса для CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Подключение всех необходимых файлов с обработкой ошибок
try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../src/Utils/UUID.php';
    require_once __DIR__ . '/../../src/Models/CommentLike.php';
    require_once __DIR__ . '/../../src/Models/Comment.php';
    require_once __DIR__ . '/../../src/Models/User.php';
    require_once __DIR__ . '/../../src/Exceptions/CommentNotFoundException.php';
    require_once __DIR__ . '/../../src/Exceptions/UserNotFoundException.php';
    require_once __DIR__ . '/../../src/Repositories/CommentLikesRepositoryInterface.php';
    require_once __DIR__ . '/../../src/Repositories/CommentLikesRepository.php';
    require_once __DIR__ . '/../../src/Repositories/CommentsRepositoryInterface.php';
    require_once __DIR__ . '/../../src/Repositories/CommentsRepository.php';
    require_once __DIR__ . '/../../src/Repositories/UsersRepositoryInterface.php';
    require_once __DIR__ . '/../../src/Repositories/UsersRepository.php';
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load required files: ' . $e->getMessage()]);
    ob_end_flush();
    exit;
}

// Инициализация базы данных
try {
    $dbConfig = new DatabaseConfig();
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Database initialization failed: ' . $e->getMessage()]);
    ob_end_flush();
    exit;
}

try {
    $commentLikesRepository = new CommentLikesRepository($pdo);
    $commentsRepository = new CommentsRepository($pdo);
    $usersRepository = new UsersRepository($pdo);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to initialize repositories: ' . $e->getMessage()]);
    ob_end_flush();
    exit;
}

// Обработка только POST запросов
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    ob_end_flush();
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    ob_end_flush();
    exit;
}

if (!isset($input['comment_uuid']) || !isset($input['user_uuid'])) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: comment_uuid, user_uuid']);
    ob_end_flush();
    exit;
}

try {
    $commentUuid = new UUID($input['comment_uuid']);
    $userUuid = new UUID($input['user_uuid']);
    
    // Проверяем существование пользователя
    try {
        $usersRepository->get($userUuid);
    } catch (UserNotFoundException $e) {
        ob_clean();
        http_response_code(404);
        echo json_encode(['error' => 'User not found: ' . $userUuid->getValue()]);
        ob_end_flush();
        exit;
    }

    // Проверяем существование комментария
    try {
        $commentsRepository->get($commentUuid);
    } catch (CommentNotFoundException $e) {
        ob_clean();
        http_response_code(404);
        echo json_encode(['error' => 'Comment not found: ' . $commentUuid->getValue()]);
        ob_end_flush();
        exit;
    }

    // Проверяем, не поставил ли уже пользователь лайк этому комментарию
    if ($commentLikesRepository->exists($commentUuid, $userUuid)) {
        ob_clean();
        http_response_code(409); // Conflict
        echo json_encode([
            'error' => 'User has already liked this comment',
            'message' => 'Пользователь уже поставил лайк этому комментарию'
        ]);
        ob_end_flush();
        exit;
    }

    // Создаем лайк
    $likeUuid = UUID::generate();
    $like = new CommentLike($likeUuid, $commentUuid, $userUuid);
    $commentLikesRepository->save($like);

    ob_clean(); // Очистить буфер перед выводом JSON
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Like created successfully',
        'data' => [
            'uuid' => $likeUuid->getValue(),
            'comment_uuid' => $commentUuid->getValue(),
            'user_uuid' => $userUuid->getValue()
        ]
    ]);

} catch (InvalidArgumentException $e) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Invalid UUID format: ' . $e->getMessage()]);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
ob_end_flush();
exit;


