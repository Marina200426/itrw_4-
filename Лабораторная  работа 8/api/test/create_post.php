<?php

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Fatal error: ' . $error['message']
        ]);
        ob_end_flush();
        exit;
    }
});

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../src/Logger/LoggerInterface.php';
    require_once __DIR__ . '/../../src/Logger/FileLogger.php';
    require_once __DIR__ . '/../../src/Utils/UUID.php';
    require_once __DIR__ . '/../../src/Models/User.php';
    require_once __DIR__ . '/../../src/Models/Post.php';
    require_once __DIR__ . '/../../src/Repositories/UsersRepositoryInterface.php';
    require_once __DIR__ . '/../../src/Repositories/UsersRepository.php';
    require_once __DIR__ . '/../../src/Repositories/PostsRepositoryInterface.php';
    require_once __DIR__ . '/../../src/Repositories/PostsRepository.php';
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load required files: ' . $e->getMessage()
    ]);
    ob_end_flush();
    exit;
}

try {
    $dbConfig = new DatabaseConfig();
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
    
    $logFile = __DIR__ . '/../../logs/app.log';
    $logger = new FileLogger($logFile);
    
    $usersRepository = new UsersRepository($pdo, $logger);
    $postsRepository = new PostsRepository($pdo, $logger);
    
    // Создаем пользователя, если его нет
    $users = $pdo->query('SELECT uuid FROM users LIMIT 1')->fetchAll();
    if (empty($users)) {
        $userUuid = UUID::generate();
        $user = new User($userUuid, 'test_author_' . time(), 'Test', 'Author');
        $usersRepository->save($user);
    } else {
        $userUuid = new UUID($users[0]['uuid']);
    }
    
    $postUuid = UUID::generate();
    $post = new Post($postUuid, $userUuid, 'Test Post ' . time(), 'This is a test post for logging demonstration.');
    $postsRepository->save($post);
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Post created successfully',
        'data' => [
            'uuid' => $postUuid->getValue(),
            'title' => $post->getTitle()
        ]
    ]);
} catch (InvalidArgumentException $e) {
    ob_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid argument: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'type' => get_class($e)
    ]);
}

ob_end_flush();
exit;

