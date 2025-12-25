<?php

// Отключение вывода ошибок в HTML (только для продакшена)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Установка заголовков до любых выводов
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка OPTIONS запроса для CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Подключение всех необходимых файлов
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Utils/UUID.php';
require_once __DIR__ . '/../src/Utils/Arguments.php';
require_once __DIR__ . '/../src/Models/Comment.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Exceptions/CommentNotFoundException.php';
require_once __DIR__ . '/../src/Exceptions/PostNotFoundException.php';
require_once __DIR__ . '/../src/Exceptions/UserNotFoundException.php';
require_once __DIR__ . '/../src/Repositories/CommentsRepositoryInterface.php';
require_once __DIR__ . '/../src/Repositories/CommentsRepository.php';
require_once __DIR__ . '/../src/Repositories/PostsRepositoryInterface.php';
require_once __DIR__ . '/../src/Repositories/PostsRepository.php';
require_once __DIR__ . '/../src/Repositories/UsersRepositoryInterface.php';
require_once __DIR__ . '/../src/Repositories/UsersRepository.php';

// Инициализация базы данных с обработкой ошибок
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
                // Игнорируем ошибки при создании (возможно уже существует)
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
                // Игнорируем ошибки при создании (возможно уже существует)
            }
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database initialization failed: ' . $e->getMessage()]);
    exit;
}

// Создание репозиториев с обработкой ошибок
try {
    $commentsRepository = new CommentsRepository($pdo);
    $postsRepository = new PostsRepository($pdo);
    $usersRepository = new UsersRepository($pdo);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to initialize repositories: ' . $e->getMessage()]);
    exit;
}

// Получение пути запроса
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Убираем /api из пути, если есть
if (strpos($path, '/api') === 0) {
    $path = substr($path, 4);
}
if (empty($path)) {
    $path = '/';
}

// Обработка специальных параметров для совместимости с разными серверами
if (isset($_GET['_method'])) {
    $requestMethod = strtoupper($_GET['_method']);
}
if (isset($_POST['_endpoint'])) {
    $path = '/' . $_POST['_endpoint'];
}

// Отладочная информация (можно убрать в продакшене)
// error_log("API Request: Method=$requestMethod, Path=$path, URI=$requestUri");

// Обработка POST /posts/comment
// Также обрабатываем запросы через index.php с параметром _endpoint
$isCommentEndpoint = ($requestMethod === 'POST' && $path === '/posts/comment') ||
                     ($requestMethod === 'POST' && isset($_POST['_endpoint']) && $_POST['_endpoint'] === 'posts/comment') ||
                     ($requestMethod === 'POST' && $path === '/index.php' && isset($_POST['_endpoint']) && $_POST['_endpoint'] === 'posts/comment') ||
                     ($requestMethod === 'POST' && ($path === '/' || $path === '/index.php') && isset($_POST['_endpoint']) && $_POST['_endpoint'] === 'posts/comment');

if ($isCommentEndpoint) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Если не удалось декодировать из body, пробуем из POST
    if (!$input && !empty($_POST) && isset($_POST['_endpoint'])) {
        $input = $_POST;
        unset($input['_endpoint']);
    }
    
    // Если все еще нет данных, пробуем получить из raw input как form-data
    if (!$input && !empty($_POST)) {
        $input = $_POST;
    }
    
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
    exit;
}

// Обработка DELETE /posts?uuid=<UUID>
// Также обрабатываем запросы через index.php с параметром _method
// И GET запросы с _method=DELETE (для совместимости с OpenServer)
$isDeleteEndpoint = ($requestMethod === 'DELETE' && $path === '/posts') ||
                    ($requestMethod === 'DELETE' && $path === '/index.php' && isset($_GET['uuid'])) ||
                    ($requestMethod === 'DELETE' && isset($_GET['uuid']) && isset($_GET['_method']) && $_GET['_method'] === 'DELETE') ||
                    ($requestMethod === 'GET' && isset($_GET['uuid']) && isset($_GET['_method']) && $_GET['_method'] === 'DELETE');

if ($isDeleteEndpoint) {
    $uuid = $_GET['uuid'] ?? null;
    $force = isset($_GET['force']) && $_GET['force'] === 'true';
    
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
    exit;
}

// Обработка GET /api/index.php?action=get_data (для совместимости)
if ($requestMethod === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_data') {
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
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch data: ' . $e->getMessage()]);
        exit;
    }
}

// Если маршрут не найден
http_response_code(404);
echo json_encode(['error' => 'Route not found']);

