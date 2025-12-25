<?php

/**
 * Endpoint для получения данных и тестирования логирования
 * GET /api/get_data.php
 */

ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Обработчик фатальных ошибок
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Fatal error: ' . $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        ob_end_flush();
        exit;
    }
});

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../src/Logger/LoggerInterface.php';
    require_once __DIR__ . '/../src/Logger/FileLogger.php';
    require_once __DIR__ . '/../src/Utils/UUID.php';
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
    
    $logFile = __DIR__ . '/../logs/app.log';
    $logger = new FileLogger($logFile);
    
    // Получаем всех пользователей
    $users = $pdo->query('SELECT uuid, username, first_name, last_name FROM users ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем все статьи
    $posts = $pdo->query('SELECT uuid, author_uuid, title, text FROM posts ORDER BY title')->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем все комментарии
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
    
    // Получаем все лайки статей
    $postLikes = $pdo->query('
        SELECT 
            pl.uuid,
            pl.post_uuid,
            pl.user_uuid,
            u.username as user_username,
            u.first_name as user_first_name,
            u.last_name as user_last_name
        FROM post_likes pl
        LEFT JOIN users u ON pl.user_uuid = u.uuid
        ORDER BY pl.uuid
    ')->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем все лайки комментариев
    $commentLikes = $pdo->query('
        SELECT 
            cl.uuid,
            cl.comment_uuid,
            cl.user_uuid,
            u.username as user_username,
            u.first_name as user_first_name,
            u.last_name as user_last_name
        FROM comment_likes cl
        LEFT JOIN users u ON cl.user_uuid = u.uuid
        ORDER BY cl.uuid
    ')->fetchAll(PDO::FETCH_ASSOC);
    
    // Подсчитываем количество лайков для каждой статьи
    foreach ($posts as &$post) {
        $post['likes_count'] = 0;
        foreach ($postLikes as $like) {
            if ($like['post_uuid'] === $post['uuid']) {
                $post['likes_count']++;
            }
        }
    }
    unset($post);
    
    // Подсчитываем количество лайков для каждого комментария
    foreach ($comments as &$comment) {
        $comment['likes_count'] = 0;
        foreach ($commentLikes as $like) {
            if ($like['comment_uuid'] === $comment['uuid']) {
                $comment['likes_count']++;
            }
        }
    }
    unset($comment);
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'users' => $users,
        'posts' => $posts,
        'comments' => $comments,
        'post_likes' => $postLikes,
        'comment_likes' => $commentLikes
    ]);
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

ob_end_flush();
exit;

