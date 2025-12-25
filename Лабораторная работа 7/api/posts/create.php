<?php

/**
 * Endpoint для создания статьи
 * POST /api/posts/create.php
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
    ob_end_flush();
    exit;
}

// Подключение всех необходимых файлов с обработкой ошибок
try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../src/Utils/UUID.php';
    require_once __DIR__ . '/../../src/Models/Post.php';
    require_once __DIR__ . '/../../src/Models/User.php';
    require_once __DIR__ . '/../../src/Exceptions/UserNotFoundException.php';
    require_once __DIR__ . '/../../src/Repositories/PostsRepositoryInterface.php';
    require_once __DIR__ . '/../../src/Repositories/PostsRepository.php';
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
    $postsRepository = new PostsRepository($pdo);
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

if (!isset($input['author_uuid']) || !isset($input['title']) || !isset($input['text'])) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: author_uuid, title, text']);
    ob_end_flush();
    exit;
}

try {
    $authorUuid = new UUID($input['author_uuid']);
    $title = trim($input['title']);
    $text = trim($input['text']);
    
    // Валидация
    if (empty($title)) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['error' => 'Title cannot be empty']);
        ob_end_flush();
        exit;
    }
    
    if (empty($text)) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['error' => 'Text cannot be empty']);
        ob_end_flush();
        exit;
    }
    
    // Проверяем существование пользователя
    try {
        $usersRepository->get($authorUuid);
    } catch (UserNotFoundException $e) {
        ob_clean();
        http_response_code(404);
        echo json_encode(['error' => 'User not found: ' . $authorUuid->getValue()]);
        ob_end_flush();
        exit;
    }

    // Создаем статью
    $postUuid = UUID::generate();
    $post = new Post($postUuid, $authorUuid, $title, $text);
    $postsRepository->save($post);

    ob_clean(); // Очистить буфер перед выводом JSON
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Post created successfully',
        'data' => [
            'uuid' => $postUuid->getValue(),
            'author_uuid' => $authorUuid->getValue(),
            'title' => $title,
            'text' => $text
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

