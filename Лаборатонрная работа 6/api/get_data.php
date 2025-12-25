<?php

/**
 * Endpoint для получения списка пользователей и статей
 * GET /api/get_data
 */

// Отключение вывода ошибок в HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Установка заголовков до любых выводов
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Подключение всех необходимых файлов с обработкой ошибок
try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../src/Utils/UUID.php';
    require_once __DIR__ . '/../src/Utils/Arguments.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load required files: ' . $e->getMessage()]);
    exit;
}

try {
    $dbConfig = new DatabaseConfig();
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
    
    // Автоматическое создание тестовых данных, если их нет
    $usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($usersCount == 0) {
        // Создаем тестовые данные
        require_once __DIR__ . '/../src/Models/User.php';
        require_once __DIR__ . '/../src/Models/Post.php';
        require_once __DIR__ . '/../src/Repositories/UsersRepository.php';
        require_once __DIR__ . '/../src/Repositories/PostsRepository.php';
        
        $usersRepo = new UsersRepository($pdo);
        $postsRepo = new PostsRepository($pdo);
        
        // Тестовые пользователи
        $testUsers = [
            ['550e8400-e29b-41d4-a716-446655440001', 'john_doe', 'John', 'Doe'],
            ['550e8400-e29b-41d4-a716-446655440002', 'jane_smith', 'Jane', 'Smith']
        ];
        
        foreach ($testUsers as $userData) {
            try {
                $user = new User(new UUID($userData[0]), $userData[1], $userData[2], $userData[3]);
                $usersRepo->save($user);
            } catch (Exception $e) {
                // Игнорируем ошибки при создании
            }
        }
        
        // Тестовые статьи
        $testPosts = [
            ['660e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440001', 'Первая тестовая статья', 'Это первая тестовая статья для проверки работы API.'],
            ['660e8400-e29b-41d4-a716-446655440002', '550e8400-e29b-41d4-a716-446655440001', 'Вторая тестовая статья', 'Это вторая тестовая статья для проверки работы API.'],
            ['660e8400-e29b-41d4-a716-446655440003', '550e8400-e29b-41d4-a716-446655440002', 'Статья от Jane', 'Это статья от другого автора для тестирования.']
        ];
        
        foreach ($testPosts as $postData) {
            try {
                $post = new Post(new UUID($postData[0]), new UUID($postData[1]), $postData[2], $postData[3]);
                $postsRepo->save($post);
            } catch (Exception $e) {
                // Игнорируем ошибки при создании
            }
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database initialization failed: ' . $e->getMessage()]);
    exit;
}

try {
    // Получаем всех пользователей
    $users = $pdo->query('SELECT uuid, username, first_name, last_name FROM users ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем все статьи
    $posts = $pdo->query('SELECT uuid, author_uuid, title, text FROM posts ORDER BY title')->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем все комментарии с информацией о статье и авторе
    $comments = $pdo->query('
        SELECT 
            c.uuid, 
            c.posts_uuid, 
            c.author_uuid, 
            c.text,
            p.title as post_title,
            u.username as author_username,
            u.first_name as author_first_name,
            u.last_name as author_last_name
        FROM comments c
        LEFT JOIN posts p ON c.posts_uuid = p.uuid
        LEFT JOIN users u ON c.author_uuid = u.uuid
        ORDER BY c.uuid
    ')->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'posts' => $posts,
        'comments' => $comments
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch data: ' . $e->getMessage()]);
}

