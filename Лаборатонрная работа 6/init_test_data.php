<?php

/**
 * Скрипт для инициализации тестовых данных
 * Запускать один раз для создания тестовых пользователей и статей
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Utils/UUID.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Post.php';
require_once __DIR__ . '/src/Repositories/UsersRepository.php';
require_once __DIR__ . '/src/Repositories/PostsRepository.php';

$dbConfig = new DatabaseConfig();
try {
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
} catch (Exception $e) {
    die("Ошибка подключения к БД: " . $e->getMessage() . "\n");
}

$usersRepository = new UsersRepository($pdo);
$postsRepository = new PostsRepository($pdo);

// Тестовые пользователи
$testUsers = [
    [
        'uuid' => '550e8400-e29b-41d4-a716-446655440001',
        'username' => 'john_doe',
        'first_name' => 'John',
        'last_name' => 'Doe'
    ],
    [
        'uuid' => '550e8400-e29b-41d4-a716-446655440002',
        'username' => 'jane_smith',
        'first_name' => 'Jane',
        'last_name' => 'Smith'
    ]
];

// Тестовые статьи
$testPosts = [
    [
        'uuid' => '660e8400-e29b-41d4-a716-446655440001',
        'author_uuid' => '550e8400-e29b-41d4-a716-446655440001',
        'title' => 'Первая тестовая статья',
        'text' => 'Это первая тестовая статья для проверки работы API.'
    ],
    [
        'uuid' => '660e8400-e29b-41d4-a716-446655440002',
        'author_uuid' => '550e8400-e29b-41d4-a716-446655440001',
        'title' => 'Вторая тестовая статья',
        'text' => 'Это вторая тестовая статья для проверки работы API.'
    ],
    [
        'uuid' => '660e8400-e29b-41d4-a716-446655440003',
        'author_uuid' => '550e8400-e29b-41d4-a716-446655440002',
        'title' => 'Статья от Jane',
        'text' => 'Это статья от другого автора для тестирования.'
    ]
];

echo "Инициализация тестовых данных...\n\n";

// Создание пользователей
echo "Создание пользователей:\n";
foreach ($testUsers as $userData) {
    try {
        $user = new User(
            new UUID($userData['uuid']),
            $userData['username'],
            $userData['first_name'],
            $userData['last_name']
        );
        $usersRepository->save($user);
        echo "✓ Пользователь создан: {$userData['username']} (UUID: {$userData['uuid']})\n";
    } catch (Exception $e) {
        echo "✗ Ошибка при создании пользователя {$userData['username']}: {$e->getMessage()}\n";
    }
}

echo "\n";

// Создание статей
echo "Создание статей:\n";
foreach ($testPosts as $postData) {
    try {
        $post = new Post(
            new UUID($postData['uuid']),
            new UUID($postData['author_uuid']),
            $postData['title'],
            $postData['text']
        );
        $postsRepository->save($post);
        echo "✓ Статья создана: {$postData['title']} (UUID: {$postData['uuid']})\n";
    } catch (Exception $e) {
        echo "✗ Ошибка при создании статьи {$postData['title']}: {$e->getMessage()}\n";
    }
}

echo "\n";
echo "Тестовые данные успешно созданы!\n\n";
echo "Доступные UUID для тестирования:\n";
echo "Пользователи:\n";
foreach ($testUsers as $user) {
    echo "  - {$user['uuid']} ({$user['username']})\n";
}
echo "\nСтатьи:\n";
foreach ($testPosts as $post) {
    echo "  - {$post['uuid']} ({$post['title']})\n";
}

