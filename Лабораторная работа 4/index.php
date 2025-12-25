<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Post.php';
require_once __DIR__ . '/src/Models/Comment.php';
require_once __DIR__ . '/src/Repositories/PostsRepository.php';
require_once __DIR__ . '/src/Repositories/CommentsRepository.php';

// Инициализация базы данных
$dbConfig = new DatabaseConfig();
try {
    $dbConfig->initializeDatabase();
    $pdo = $dbConfig->getConnection();
} catch (Exception $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

$postsRepository = new PostsRepository($pdo);
$commentsRepository = new CommentsRepository($pdo);

// Обработка действий
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_user':
            try {
                $uuid = $_POST['user_uuid'] ?? '';
                $username = $_POST['username'] ?? '';
                $firstName = $_POST['first_name'] ?? '';
                $lastName = $_POST['last_name'] ?? '';
                
                if ($uuid && $username && $firstName && $lastName) {
                    $stmt = $pdo->prepare('INSERT OR REPLACE INTO users (uuid, username, first_name, last_name) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$uuid, $username, $firstName, $lastName]);
                    $message = "Пользователь успешно создан!";
                    $messageType = 'success';
                } else {
                    $message = "Заполните все поля!";
                    $messageType = 'error';
                }
            } catch (Exception $e) {
                $message = "Ошибка: " . $e->getMessage();
                $messageType = 'error';
            }
            break;
            
        case 'create_post':
            try {
                $uuid = $_POST['post_uuid'] ?? '';
                $authorUuid = $_POST['author_uuid'] ?? '';
                $title = $_POST['title'] ?? '';
                $text = $_POST['text'] ?? '';
                
                if ($uuid && $authorUuid && $title && $text) {
                    $post = new Post($uuid, $authorUuid, $title, $text);
                    $postsRepository->save($post);
                    $message = "Статья успешно создана!";
                    $messageType = 'success';
                } else {
                    $message = "Заполните все поля!";
                    $messageType = 'error';
                }
            } catch (Exception $e) {
                $message = "Ошибка: " . $e->getMessage();
                $messageType = 'error';
            }
            break;
            
        case 'create_comment':
            try {
                $uuid = $_POST['comment_uuid'] ?? '';
                $postsUuid = $_POST['posts_uuid'] ?? '';
                $authorUuid = $_POST['comment_author_uuid'] ?? '';
                $text = $_POST['comment_text'] ?? '';
                
                if ($uuid && $postsUuid && $authorUuid && $text) {
                    $comment = new Comment($uuid, $postsUuid, $authorUuid, $text);
                    $commentsRepository->save($comment);
                    $message = "Комментарий успешно создан!";
                    $messageType = 'success';
                } else {
                    $message = "Заполните все поля!";
                    $messageType = 'error';
                }
            } catch (Exception $e) {
                $message = "Ошибка: " . $e->getMessage();
                $messageType = 'error';
            }
            break;
            
        case 'get_post':
            try {
                $uuid = $_POST['get_post_uuid'] ?? '';
                if ($uuid) {
                    $post = $postsRepository->get($uuid);
                    if ($post) {
                        $_SESSION['found_post'] = $post;
                        $message = "Статья найдена!";
                        $messageType = 'success';
                    } else {
                        $message = "Статья не найдена!";
                        $messageType = 'error';
                    }
                }
            } catch (Exception $e) {
                $message = "Ошибка: " . $e->getMessage();
                $messageType = 'error';
            }
            break;
            
        case 'get_comment':
            try {
                $uuid = $_POST['get_comment_uuid'] ?? '';
                if ($uuid) {
                    $comment = $commentsRepository->get($uuid);
                    if ($comment) {
                        $_SESSION['found_comment'] = $comment;
                        $message = "Комментарий найден!";
                        $messageType = 'success';
                    } else {
                        $message = "Комментарий не найден!";
                        $messageType = 'error';
                    }
                }
            } catch (Exception $e) {
                $message = "Ошибка: " . $e->getMessage();
                $messageType = 'error';
            }
            break;
    }
}

// Получение всех данных для отображения
$users = $pdo->query('SELECT * FROM users ORDER BY username')->fetchAll();
$posts = $pdo->query('SELECT p.*, u.username, u.first_name, u.last_name FROM posts p LEFT JOIN users u ON p.author_uuid = u.uuid ORDER BY p.title')->fetchAll();
$comments = $pdo->query('SELECT c.*, u.username, u.first_name, u.last_name, p.title as post_title FROM comments c LEFT JOIN users u ON c.author_uuid = u.uuid LEFT JOIN posts p ON c.posts_uuid = p.uuid ORDER BY c.uuid')->fetchAll();

// Генерация UUID для помощи пользователю
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 4 - Тестирование</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Лабораторная работа 4</h1>
            <p class="subtitle">Тестирование работы с базой данных SQLite (Статьи и Комментарии)</p>
        </header>

        <?php if ($message): ?>
        <div class="message message-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-button active" onclick="showTab('create')">Создание</button>
            <button class="tab-button" onclick="showTab('view')">Просмотр</button>
            <button class="tab-button" onclick="showTab('search')">Поиск</button>
        </div>

        <!-- Вкладка создания -->
        <div id="create-tab" class="tab-content active">
            <div class="cards-grid">
                <!-- Создание пользователя -->
                <div class="card">
                    <h2>👤 Создать пользователя</h2>
                    <form method="POST" class="form">
                        <input type="hidden" name="action" value="create_user">
                        <div class="form-group">
                            <label>UUID пользователя</label>
                            <div class="input-with-button">
                                <input type="text" name="user_uuid" required placeholder="550e8400-e29b-41d4-a716-446655440000">
                                <button type="button" class="btn-generate" onclick="generateUUID('user_uuid')">🎲</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Имя пользователя</label>
                            <input type="text" name="username" required placeholder="john_doe">
                        </div>
                        <div class="form-group">
                            <label>Имя</label>
                            <input type="text" name="first_name" required placeholder="John">
                        </div>
                        <div class="form-group">
                            <label>Фамилия</label>
                            <input type="text" name="last_name" required placeholder="Doe">
                        </div>
                        <button type="submit" class="btn btn-primary">Создать пользователя</button>
                    </form>
                </div>

                <!-- Создание статьи -->
                <div class="card">
                    <h2>📝 Создать статью</h2>
                    <form method="POST" class="form">
                        <input type="hidden" name="action" value="create_post">
                        <div class="form-group">
                            <label>UUID статьи</label>
                            <div class="input-with-button">
                                <input type="text" name="post_uuid" required placeholder="660e8400-e29b-41d4-a716-446655440001">
                                <button type="button" class="btn-generate" onclick="generateUUID('post_uuid')">🎲</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>UUID автора</label>
                            <select name="author_uuid" required>
                                <option value="">Выберите пользователя</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user['uuid']); ?>">
                                        <?php echo htmlspecialchars($user['username'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Заголовок</label>
                            <input type="text" name="title" required placeholder="Заголовок статьи">
                        </div>
                        <div class="form-group">
                            <label>Текст статьи</label>
                            <textarea name="text" required rows="4" placeholder="Текст статьи..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Создать статью</button>
                    </form>
                </div>

                <!-- Создание комментария -->
                <div class="card">
                    <h2>💬 Создать комментарий</h2>
                    <form method="POST" class="form">
                        <input type="hidden" name="action" value="create_comment">
                        <div class="form-group">
                            <label>UUID комментария</label>
                            <div class="input-with-button">
                                <input type="text" name="comment_uuid" required placeholder="770e8400-e29b-41d4-a716-446655440002">
                                <button type="button" class="btn-generate" onclick="generateUUID('comment_uuid')">🎲</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>UUID статьи</label>
                            <select name="posts_uuid" required>
                                <option value="">Выберите статью</option>
                                <?php foreach ($posts as $post): ?>
                                    <option value="<?php echo htmlspecialchars($post['uuid']); ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>UUID автора</label>
                            <select name="comment_author_uuid" required>
                                <option value="">Выберите пользователя</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user['uuid']); ?>">
                                        <?php echo htmlspecialchars($user['username'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Текст комментария</label>
                            <textarea name="comment_text" required rows="3" placeholder="Текст комментария..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Создать комментарий</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Вкладка просмотра -->
        <div id="view-tab" class="tab-content">
            <!-- Пользователи -->
            <div class="section">
                <h2>👥 Пользователи (<?php echo count($users); ?>)</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>UUID</th>
                                <th>Имя пользователя</th>
                                <th>Имя</th>
                                <th>Фамилия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr><td colspan="4" class="empty">Нет пользователей</td></tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="uuid-cell"><?php echo htmlspecialchars($user['uuid']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Статьи -->
            <div class="section">
                <h2>📰 Статьи (<?php echo count($posts); ?>)</h2>
                <div class="posts-grid">
                    <?php if (empty($posts)): ?>
                        <div class="empty">Нет статей</div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="post-card">
                                <div class="post-header">
                                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <span class="post-author">
                                        👤 <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name'] . ' (@' . $post['username'] . ')'); ?>
                                    </span>
                                </div>
                                <p class="post-text"><?php echo nl2br(htmlspecialchars($post['text'])); ?></p>
                                <div class="post-footer">
                                    <span class="post-uuid">UUID: <?php echo htmlspecialchars($post['uuid']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Комментарии -->
            <div class="section">
                <h2>💬 Комментарии (<?php echo count($comments); ?>)</h2>
                <div class="comments-list">
                    <?php if (empty($comments)): ?>
                        <div class="empty">Нет комментариев</div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-card">
                                <div class="comment-header">
                                    <span class="comment-author">
                                        👤 <?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name'] . ' (@' . $comment['username'] . ')'); ?>
                                    </span>
                                    <span class="comment-post">📝 К статье: <?php echo htmlspecialchars($comment['post_title']); ?></span>
                                </div>
                                <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['text'])); ?></p>
                                <div class="comment-footer">
                                    <span class="comment-uuid">UUID: <?php echo htmlspecialchars($comment['uuid']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Вкладка поиска -->
        <div id="search-tab" class="tab-content">
            <div class="cards-grid">
                <!-- Поиск статьи -->
                <div class="card">
                    <h2>🔍 Найти статью</h2>
                    <form method="POST" class="form">
                        <input type="hidden" name="action" value="get_post">
                        <div class="form-group">
                            <label>UUID статьи</label>
                            <input type="text" name="get_post_uuid" required placeholder="Введите UUID статьи">
                        </div>
                        <button type="submit" class="btn btn-primary">Найти</button>
                    </form>
                    <?php if (isset($_SESSION['found_post'])): ?>
                        <?php $post = $_SESSION['found_post']; unset($_SESSION['found_post']); ?>
                        <div class="result-card">
                            <h3>Результат поиска:</h3>
                            <p><strong>UUID:</strong> <?php echo htmlspecialchars($post->getUuid()); ?></p>
                            <p><strong>Заголовок:</strong> <?php echo htmlspecialchars($post->getTitle()); ?></p>
                            <p><strong>Текст:</strong> <?php echo nl2br(htmlspecialchars($post->getText())); ?></p>
                            <p><strong>UUID автора:</strong> <?php echo htmlspecialchars($post->getAuthorUuid()); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Поиск комментария -->
                <div class="card">
                    <h2>🔍 Найти комментарий</h2>
                    <form method="POST" class="form">
                        <input type="hidden" name="action" value="get_comment">
                        <div class="form-group">
                            <label>UUID комментария</label>
                            <input type="text" name="get_comment_uuid" required placeholder="Введите UUID комментария">
                        </div>
                        <button type="submit" class="btn btn-primary">Найти</button>
                    </form>
                    <?php if (isset($_SESSION['found_comment'])): ?>
                        <?php $comment = $_SESSION['found_comment']; unset($_SESSION['found_comment']); ?>
                        <div class="result-card">
                            <h3>Результат поиска:</h3>
                            <p><strong>UUID:</strong> <?php echo htmlspecialchars($comment->getUuid()); ?></p>
                            <p><strong>Текст:</strong> <?php echo nl2br(htmlspecialchars($comment->getText())); ?></p>
                            <p><strong>UUID статьи:</strong> <?php echo htmlspecialchars($comment->getPostsUuid()); ?></p>
                            <p><strong>UUID автора:</strong> <?php echo htmlspecialchars($comment->getAuthorUuid()); ?></p>
                        </div>
                    <?php endif; ?>
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

        function generateUUID(fieldName) {
            // Генерация UUID v4
            function uuidv4() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0;
                    const v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }
            const uuid = uuidv4();
            document.querySelector(`input[name="${fieldName}"]`).value = uuid;
        }
    </script>
</body>
</html>

