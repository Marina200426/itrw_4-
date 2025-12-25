<?php

/**
 * Пример использования репозиториев
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Post.php';
require_once __DIR__ . '/src/Models/Comment.php';
require_once __DIR__ . '/src/Repositories/PostsRepository.php';
require_once __DIR__ . '/src/Repositories/CommentsRepository.php';

// Инициализация базы данных
$dbConfig = new DatabaseConfig();
$dbConfig->initializeDatabase();
$pdo = $dbConfig->getConnection();

// Создание репозиториев
$postsRepository = new PostsRepository($pdo);
$commentsRepository = new CommentsRepository($pdo);

// Создание пользователя (для примера)
$userUuid = '550e8400-e29b-41d4-a716-446655440000';
$user = new User($userUuid, 'john_doe', 'John', 'Doe');

// Сохранение пользователя (если нужен репозиторий для пользователей)
$stmt = $pdo->prepare('INSERT OR REPLACE INTO users (uuid, username, first_name, last_name) VALUES (?, ?, ?, ?)');
$stmt->execute([$user->getUuid(), $user->getUsername(), $user->getFirstName(), $user->getLastName()]);

// Создание и сохранение статьи
$postUuid = '660e8400-e29b-41d4-a716-446655440001';
$post = new Post(
    $postUuid,
    $userUuid,
    'Моя первая статья',
    'Это текст моей первой статьи на PHP.'
);

$postsRepository->save($post);
echo "Статья сохранена: {$post->getTitle()}\n";

// Получение статьи
$retrievedPost = $postsRepository->get($postUuid);
if ($retrievedPost) {
    echo "Статья найдена: {$retrievedPost->getTitle()}\n";
}

// Создание и сохранение комментария
$commentUuid = '770e8400-e29b-41d4-a716-446655440002';
$comment = new Comment(
    $commentUuid,
    $postUuid,
    $userUuid,
    'Отличная статья!'
);

$commentsRepository->save($comment);
echo "Комментарий сохранен: {$comment->getText()}\n";

// Получение комментария
$retrievedComment = $commentsRepository->get($commentUuid);
if ($retrievedComment) {
    echo "Комментарий найден: {$retrievedComment->getText()}\n";
}

echo "\nВсе операции выполнены успешно!\n";

