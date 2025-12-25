<?php

/**
 * Скрипт для инициализации тестовых данных
 * Запуск: php init_test_data.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Logger/LoggerInterface.php';
require_once __DIR__ . '/src/Logger/FileLogger.php';
require_once __DIR__ . '/src/Utils/UUID.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Post.php';
require_once __DIR__ . '/src/Models/Comment.php';
require_once __DIR__ . '/src/Models/Like.php';
require_once __DIR__ . '/src/Models/CommentLike.php';
require_once __DIR__ . '/src/Repositories/UsersRepositoryInterface.php';
require_once __DIR__ . '/src/Repositories/UsersRepository.php';
require_once __DIR__ . '/src/Repositories/PostsRepositoryInterface.php';
require_once __DIR__ . '/src/Repositories/PostsRepository.php';
require_once __DIR__ . '/src/Repositories/CommentsRepositoryInterface.php';
require_once __DIR__ . '/src/Repositories/CommentsRepository.php';
require_once __DIR__ . '/src/Repositories/LikesRepositoryInterface.php';
require_once __DIR__ . '/src/Repositories/LikesRepository.php';
require_once __DIR__ . '/src/Repositories/CommentLikesRepositoryInterface.php';
require_once __DIR__ . '/src/Repositories/CommentLikesRepository.php';

echo "Инициализация тестовых данных...\n\n";

try {
    $dbConfig = new DatabaseConfig();
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
    
    $logFile = __DIR__ . '/logs/app.log';
    $logger = new FileLogger($logFile);
    
    $usersRepository = new UsersRepository($pdo, $logger);
    $postsRepository = new PostsRepository($pdo, $logger);
    $commentsRepository = new CommentsRepository($pdo, $logger);
    $likesRepository = new LikesRepository($pdo, $logger);
    $commentLikesRepository = new CommentLikesRepository($pdo, $logger);
    
    // Проверяем, есть ли уже данные
    $usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($usersCount > 0) {
        echo "В базе данных уже есть данные. Пропускаем создание.\n";
        exit(0);
    }
    
    echo "Создание пользователей...\n";
    $users = [];
    
    // Создаем 5 пользователей
    $userData = [
        ['Иван', 'Петров'],
        ['Мария', 'Сидорова'],
        ['Алексей', 'Иванов'],
        ['Елена', 'Козлова'],
        ['Дмитрий', 'Смирнов']
    ];
    
    foreach ($userData as $index => $data) {
        $userUuid = UUID::generate();
        $username = 'user_' . strtolower($data[0]) . '_' . ($index + 1);
        $user = new User($userUuid, $username, $data[0], $data[1]);
        $usersRepository->save($user);
        $users[] = $user;
        echo "  ✓ Создан пользователь: {$data[0]} {$data[1]} ({$userUuid->getValue()})\n";
    }
    
    echo "\nСоздание статей...\n";
    $posts = [];
    
    $postTitles = [
        'Введение в логирование в PHP',
        'Лучшие практики работы с репозиториями',
        'Тестирование приложений с логированием',
        'Архитектура приложений с зависимостями',
        'Работа с SQLite в PHP'
    ];
    
    $postTexts = [
        'Логирование - это важный инструмент для отслеживания работы приложения. В этой статье мы рассмотрим основы.',
        'Репозитории позволяют абстрагировать работу с базой данных. Это упрощает тестирование и поддержку кода.',
        'Тестирование с логированием помогает отслеживать поведение приложения и находить ошибки на ранних этапах.',
        'Правильная архитектура с зависимостями делает код более гибким и легко тестируемым.',
        'SQLite - это легковесная база данных, которая отлично подходит для небольших приложений и тестирования.'
    ];
    
    foreach ($postTitles as $index => $title) {
        $postUuid = UUID::generate();
        $author = $users[$index % count($users)];
        $post = new Post($postUuid, $author->getUuid(), $title, $postTexts[$index]);
        $postsRepository->save($post);
        $posts[] = $post;
        echo "  ✓ Создана статья: {$title} ({$postUuid->getValue()})\n";
    }
    
    echo "\nСоздание комментариев...\n";
    $comments = [];
    
    $commentTexts = [
        'Отличная статья! Очень полезная информация.',
        'Спасибо за подробное объяснение.',
        'Хотелось бы увидеть больше примеров.',
        'Это помогло мне понять концепцию.',
        'Отличный материал для изучения.',
        'Буду применять эти знания на практике.',
        'Очень понятно объяснено.',
        'Рекомендую к прочтению всем!'
    ];
    
    foreach ($commentTexts as $index => $text) {
        $commentUuid = UUID::generate();
        $post = $posts[$index % count($posts)];
        $author = $users[($index + 1) % count($users)];
        $comment = new Comment($commentUuid, $post->getUuid(), $author->getUuid(), $text);
        $commentsRepository->save($comment);
        $comments[] = $comment;
        echo "  ✓ Создан комментарий к статье \"{$post->getTitle()}\" ({$commentUuid->getValue()})\n";
    }
    
    echo "\nСоздание лайков для статей...\n";
    $likesCount = 0;
    
    // Каждая статья получает лайки от разных пользователей
    foreach ($posts as $postIndex => $post) {
        $likesForPost = min(3, count($users)); // До 3 лайков на статью
        for ($i = 0; $i < $likesForPost; $i++) {
            $userIndex = ($postIndex + $i) % count($users);
            $likeUuid = UUID::generate();
            $like = new Like($likeUuid, $post->getUuid(), $users[$userIndex]->getUuid());
            $likesRepository->save($like);
            $likesCount++;
        }
    }
    echo "  ✓ Создано {$likesCount} лайков для статей\n";
    
    echo "\nСоздание лайков для комментариев...\n";
    $commentLikesCount = 0;
    
    // Каждый комментарий получает лайки
    foreach ($comments as $commentIndex => $comment) {
        $likesForComment = min(2, count($users)); // До 2 лайков на комментарий
        for ($i = 0; $i < $likesForComment; $i++) {
            $userIndex = ($commentIndex + $i + 1) % count($users);
            $likeUuid = UUID::generate();
            $like = new CommentLike($likeUuid, $comment->getUuid(), $users[$userIndex]->getUuid());
            $commentLikesRepository->save($like);
            $commentLikesCount++;
        }
    }
    echo "  ✓ Создано {$commentLikesCount} лайков для комментариев\n";
    
    echo "\n✅ Тестовые данные успешно созданы!\n";
    echo "\nСтатистика:\n";
    echo "  - Пользователей: " . count($users) . "\n";
    echo "  - Статей: " . count($posts) . "\n";
    echo "  - Комментариев: " . count($comments) . "\n";
    echo "  - Лайков статей: {$likesCount}\n";
    echo "  - Лайков комментариев: {$commentLikesCount}\n";
    echo "\nВсе операции были залогированы. Проверьте файл logs/app.log\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

