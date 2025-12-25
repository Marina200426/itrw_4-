<?php

/**
 * Скрипт для ручного создания тестовых данных
 * Можно запустить через браузер или командную строку
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Utils/UUID.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Post.php';
require_once __DIR__ . '/src/Repositories/UsersRepository.php';
require_once __DIR__ . '/src/Repositories/PostsRepository.php';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Создание тестовых данных</title></head><body>";
echo "<h1>Создание тестовых данных</h1>";

try {
    $dbConfig = new DatabaseConfig();
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
    
    $usersRepo = new UsersRepository($pdo);
    $postsRepo = new PostsRepository($pdo);
    
    // Проверяем, есть ли уже данные
    $usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $postsCount = $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    
    if ($usersCount > 0 || $postsCount > 0) {
        echo "<p style='color: orange;'>В базе данных уже есть данные: $usersCount пользователей, $postsCount статей.</p>";
        echo "<p>Если хотите пересоздать данные, удалите файл database/app.db и обновите страницу.</p>";
        echo "<p><a href='index.php'>Вернуться на главную</a></p>";
        echo "</body></html>";
        exit;
    }
    
    echo "<p>Создание тестовых данных...</p><ul>";
    
    // Тестовые пользователи
    $testUsers = [
        ['550e8400-e29b-41d4-a716-446655440001', 'john_doe', 'John', 'Doe'],
        ['550e8400-e29b-41d4-a716-446655440002', 'jane_smith', 'Jane', 'Smith']
    ];
    
    foreach ($testUsers as $userData) {
        try {
            $user = new User(new UUID($userData[0]), $userData[1], $userData[2], $userData[3]);
            $usersRepo->save($user);
            echo "<li style='color: green;'>✓ Пользователь создан: {$userData[1]} (UUID: {$userData[0]})</li>";
        } catch (Exception $e) {
            echo "<li style='color: red;'>✗ Ошибка при создании пользователя {$userData[1]}: {$e->getMessage()}</li>";
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
            echo "<li style='color: green;'>✓ Статья создана: {$postData[2]} (UUID: {$postData[0]})</li>";
        } catch (Exception $e) {
            echo "<li style='color: red;'>✗ Ошибка при создании статьи {$postData[2]}: {$e->getMessage()}</li>";
        }
    }
    
    echo "</ul>";
    echo "<p style='color: green; font-weight: bold;'>Тестовые данные успешно созданы!</p>";
    echo "<p><a href='index.php'>Вернуться на главную</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка: {$e->getMessage()}</p>";
}

echo "</body></html>";

