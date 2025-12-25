<?php

/**
 * Endpoint для создания комментария
 * POST /api/posts/comment.php
 */

// Отключение вывода ошибок в HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Установка заголовков до любых выводов
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка OPTIONS запроса для CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Подключение всех необходимых файлов
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Utils/UUID.php';
require_once __DIR__ . '/../../src/Utils/Arguments.php';
require_once __DIR__ . '/../../src/Models/Comment.php';
require_once __DIR__ . '/../../src/Models/Post.php';
require_once __DIR__ . '/../../src/Models/User.php';
require_once __DIR__ . '/../../src/Exceptions/CommentNotFoundException.php';
require_once __DIR__ . '/../../src/Exceptions/PostNotFoundException.php';
require_once __DIR__ . '/../../src/Exceptions/UserNotFoundException.php';
require_once __DIR__ . '/../../src/Repositories/CommentsRepositoryInterface.php';
require_once __DIR__ . '/../../src/Repositories/CommentsRepository.php';
require_once __DIR__ . '/../../src/Repositories/PostsRepositoryInterface.php';
require_once __DIR__ . '/../../src/Repositories/PostsRepository.php';
require_once __DIR__ . '/../../src/Repositories/UsersRepositoryInterface.php';
require_once __DIR__ . '/../../src/Repositories/UsersRepository.php';

// Инициализация базы данных
try {
    $dbConfig = new DatabaseConfig();
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
    
    // Автоматическое создание тестовых данных, если их нет
    $usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($usersCount == 0) {
        require_once __DIR__ . '/../../src/Models/User.php';
        require_once __DIR__ . '/../../src/Models/Post.php';
        require_once __DIR__ . '/../../src/Repositories/UsersRepository.php';
        require_once __DIR__ . '/../../src/Repositories/PostsRepository.php';
        
        $usersRepo = new UsersRepository($pdo);
        $postsRepo = new PostsRepository($pdo);
        
        $testUsers = [
            ['550e8400-e29b-41d4-a716-446655440001', 'john_doe', 'John', 'Doe'],
            ['550e8400-e29b-41d4-a716-446655440002', 'jane_smith', 'Jane', 'Smith']
        ];
        
        foreach ($testUsers as $userData) {
            try {
                $user = new User(new UUID($userData[0]), $userData[1], $userData[2], $userData[3]);
                $usersRepo->save($user);
            } catch (Exception $e) {}
        }
        
        $testPosts = [
            ['660e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440001', 'Первая тестовая статья', 'Это первая тестовая статья для проверки работы API.'],
            ['660e8400-e29b-41d4-a716-446655440002', '550e8400-e29b-41d4-a716-446655440001', 'Вторая тестовая статья', 'Это вторая тестовая статья для проверки работы API.'],
            ['660e8400-e29b-41d4-a716-446655440003', '550e8400-e29b-41d4-a716-446655440002', 'Статья от Jane', 'Это статья от другого автора для тестирования.']
        ];
        
        foreach ($testPosts as $postData) {
            try {
                $post = new Post(new UUID($postData[0]), new UUID($postData[1]), $postData[2], $postData[3]);
                $postsRepo->save($post);
            } catch (Exception $e) {}
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database initialization failed: ' . $e->getMessage()]);
    exit;
}

$commentsRepository = new CommentsRepository($pdo);
$postsRepository = new PostsRepository($pdo);
$usersRepository = new UsersRepository($pdo);

// Обработка только POST запросов
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Проверка наличия обязательных полей
if (!isset($input['author_uuid']) || !isset($input['post_uuid']) || !isset($input['text'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: author_uuid, post_uuid, text']);
    exit;
}

try {
    // Валидация UUID
    $authorUuid = new UUID($input['author_uuid']);
    $postUuid = new UUID($input['post_uuid']);
    
    // Проверка существования пользователя
    try {
        $usersRepository->get($authorUuid);
    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found: ' . $authorUuid->getValue()]);
        exit;
    }

    // Проверка существования статьи
    try {
        $postsRepository->get($postUuid);
    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode(['error' => 'Post not found: ' . $postUuid->getValue()]);
        exit;
    }

    // Валидация текста комментария
    if (empty(trim($input['text']))) {
        http_response_code(400);
        echo json_encode(['error' => 'Text cannot be empty']);
        exit;
    }

    // Создание комментария
    $commentUuid = UUID::generate();
    $comment = new Comment($commentUuid, $postUuid, $authorUuid, $input['text']);
    $commentsRepository->save($comment);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Comment created successfully',
        'data' => [
            'uuid' => $commentUuid->getValue(),
            'post_uuid' => $postUuid->getValue(),
            'author_uuid' => $authorUuid->getValue(),
            'text' => $input['text']
        ]
    ]);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid UUID format']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}

