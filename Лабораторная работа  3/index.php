<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Faker\Factory;

// Инициализация Faker
$faker = Factory::create('ru_RU');

// Генерация тестовых данных
$users = [];
$posts = [];
$comments = [];

// Генерация пользователей
for ($i = 1; $i <= 10; $i++) {
    $users[] = new User($i, $faker->firstName, $faker->lastName);
}

// Генерация статей
$articleTitles = [
    'Введение в программирование на PHP',
    'Основы объектно-ориентированного программирования',
    'Работа с базами данных в PHP',
    'Автозагрузка классов и пространства имён',
    'Современные практики разработки на PHP',
    'Использование Composer в проектах',
    'Паттерны проектирования в PHP',
    'Работа с API и веб-сервисами',
    'Безопасность веб-приложений',
    'Тестирование PHP приложений',
    'Оптимизация производительности',
    'Работа с файлами и директориями',
    'Обработка ошибок и исключений',
    'Сессии и cookies в PHP',
    'Создание RESTful API'
];

for ($i = 1; $i <= 15; $i++) {
    $authorId = $faker->numberBetween(1, count($users));
    $title = $articleTitles[$i - 1] ?? $faker->realText(50);
    $text = $faker->realText(500) . "\n\n" . $faker->realText(300);
    $posts[] = new Post(
        $i,
        $authorId,
        $title,
        $text
    );
}

// Генерация комментариев
for ($i = 1; $i <= 25; $i++) {
    $authorId = $faker->numberBetween(1, count($users));
    $postId = $faker->numberBetween(1, count($posts));
    $comments[] = new Comment(
        $i,
        $authorId,
        $postId,
        $faker->realText(150)
    );
}

// Функция для получения пользователя по ID
function getUserById(array $users, int $id): ?User
{
    foreach ($users as $user) {
        if ($user->getId() === $id) {
            return $user;
        }
    }
    return null;
}

// Функция для получения статьи по ID
function getPostById(array $posts, int $id): ?Post
{
    foreach ($posts as $post) {
        if ($post->getId() === $id) {
            return $post;
        }
    }
    return null;
}

// Функция для получения комментариев к статье
function getCommentsByPostId(array $comments, int $postId): array
{
    $result = [];
    foreach ($comments as $comment) {
        if ($comment->getPostId() === $postId) {
            $result[] = $comment;
        }
    }
    return $result;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 3 - Автозагрузчик классов</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔧 Лабораторная работа 3</h1>
            <p class="subtitle">Автозагрузчик классов с PSR-4 и визуализация данных</p>
        </header>

        <div class="tabs">
            <button class="tab-button active" onclick="showTab('users')">👥 Пользователи</button>
            <button class="tab-button" onclick="showTab('posts')">📝 Статьи</button>
            <button class="tab-button" onclick="showTab('comments')">💬 Комментарии</button>
            <button class="tab-button" onclick="showTab('overview')">📊 Обзор</button>
        </div>

        <!-- Вкладка пользователей -->
        <div id="users-tab" class="tab-content active">
            <div class="section">
                <h2>👥 Пользователи (<?php echo count($users); ?>)</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Фамилия</th>
                                <th>Полное имя</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="id-cell"><?php echo htmlspecialchars($user->getId()); ?></td>
                                    <td><?php echo htmlspecialchars($user->getFirstName()); ?></td>
                                    <td><?php echo htmlspecialchars($user->getLastName()); ?></td>
                                    <td><strong><?php echo htmlspecialchars($user->getFullName()); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Вкладка статей -->
        <div id="posts-tab" class="tab-content">
            <div class="section">
                <h2>📝 Статьи (<?php echo count($posts); ?>)</h2>
                <div class="posts-grid">
                    <?php foreach ($posts as $post): ?>
                        <?php $author = getUserById($users, $post->getAuthorId()); ?>
                        <div class="post-card">
                            <div class="post-header">
                                <h3><?php echo htmlspecialchars($post->getTitle()); ?></h3>
                                <span class="post-author">
                                    👤 Автор: <?php echo $author ? htmlspecialchars($author->getFullName()) : 'Неизвестен'; ?> (ID: <?php echo $post->getAuthorId(); ?>)
                                </span>
                            </div>
                            <p class="post-text"><?php echo nl2br(htmlspecialchars($post->getText())); ?></p>
                            <div class="post-footer">
                                <span class="post-id">ID статьи: <?php echo htmlspecialchars($post->getId()); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Вкладка комментариев -->
        <div id="comments-tab" class="tab-content">
            <div class="section">
                <h2>💬 Комментарии (<?php echo count($comments); ?>)</h2>
                <div class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <?php 
                        $author = getUserById($users, $comment->getAuthorId());
                        $post = getPostById($posts, $comment->getPostId());
                        ?>
                        <div class="comment-card">
                            <div class="comment-header">
                                <span class="comment-author">
                                    👤 <?php echo $author ? htmlspecialchars($author->getFullName()) : 'Неизвестен'; ?> (ID: <?php echo $comment->getAuthorId(); ?>)
                                </span>
                                <span class="comment-post">
                                    📝 К статье: "<?php echo $post ? htmlspecialchars($post->getTitle()) : 'Неизвестна'; ?>" (ID: <?php echo $comment->getPostId(); ?>)
                                </span>
                            </div>
                            <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment->getText())); ?></p>
                            <div class="comment-footer">
                                <span class="comment-id">ID комментария: <?php echo htmlspecialchars($comment->getId()); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Вкладка обзора -->
        <div id="overview-tab" class="tab-content">
            <div class="section">
                <h2>📊 Обзор данных</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-value"><?php echo count($users); ?></div>
                        <div class="stat-label">Пользователей</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📝</div>
                        <div class="stat-value"><?php echo count($posts); ?></div>
                        <div class="stat-label">Статей</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">💬</div>
                        <div class="stat-value"><?php echo count($comments); ?></div>
                        <div class="stat-label">Комментариев</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>📝 Статьи с комментариями</h2>
                <div class="posts-with-comments">
                    <?php foreach ($posts as $post): ?>
                        <?php 
                        $author = getUserById($users, $post->getAuthorId());
                        $postComments = getCommentsByPostId($comments, $post->getId());
                        ?>
                        <div class="post-with-comments-card">
                            <div class="post-header">
                                <h3><?php echo htmlspecialchars($post->getTitle()); ?></h3>
                                <span class="post-author">
                                    👤 Автор: <?php echo $author ? htmlspecialchars($author->getFullName()) : 'Неизвестен'; ?>
                                </span>
                            </div>
                            <p class="post-text"><?php echo nl2br(htmlspecialchars($post->getText())); ?></p>
                            
                            <?php if (!empty($postComments)): ?>
                                <div class="comments-section">
                                    <h4>💬 Комментарии (<?php echo count($postComments); ?>):</h4>
                                    <?php foreach ($postComments as $comment): ?>
                                        <?php $commentAuthor = getUserById($users, $comment->getAuthorId()); ?>
                                        <div class="nested-comment">
                                            <span class="comment-author-small">
                                                👤 <?php echo $commentAuthor ? htmlspecialchars($commentAuthor->getFullName()) : 'Неизвестен'; ?>
                                            </span>
                                            <p><?php echo nl2br(htmlspecialchars($comment->getText())); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="no-comments">Нет комментариев</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Скрыть все вкладки
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Убрать активный класс у всех кнопок
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Показать выбранную вкладку
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Добавить активный класс к кнопке
            event.target.classList.add('active');
        }
    </script>
</body>
</html>

