<?php

require_once __DIR__ . '/../src/Utils/UUID.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Models/Comment.php';
require_once __DIR__ . '/../src/Models/Like.php';
require_once __DIR__ . '/../src/Models/CommentLike.php';
require_once __DIR__ . '/../src/Logger/LoggerInterface.php';
require_once __DIR__ . '/../src/Logger/TestLogger.php';
require_once __DIR__ . '/../src/Repositories/UsersRepository.php';
require_once __DIR__ . '/../src/Repositories/PostsRepository.php';
require_once __DIR__ . '/../src/Repositories/CommentsRepository.php';
require_once __DIR__ . '/../src/Repositories/LikesRepository.php';
require_once __DIR__ . '/../src/Repositories/CommentLikesRepository.php';
require_once __DIR__ . '/../src/Exceptions/UserNotFoundException.php';
require_once __DIR__ . '/../src/Exceptions/PostNotFoundException.php';
require_once __DIR__ . '/../src/Exceptions/CommentNotFoundException.php';

class RepositoriesTest
{
    private ?PDO $pdo = null;
    private ?string $testDbPath = null;
    private ?TestLogger $logger = null;

    public function setUp(): void
    {
        $this->testDbPath = sys_get_temp_dir() . '/test_repos_' . uniqid() . '.db';
        $pdo = new PDO('sqlite:' . $this->testDbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Создаем таблицы
        $pdo->exec('CREATE TABLE IF NOT EXISTS users (
            uuid TEXT PRIMARY KEY NOT NULL,
            username TEXT NOT NULL UNIQUE,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL
        )');
        
        $pdo->exec('CREATE TABLE IF NOT EXISTS posts (
            uuid TEXT PRIMARY KEY NOT NULL,
            author_uuid TEXT NOT NULL,
            title TEXT NOT NULL,
            text TEXT NOT NULL
        )');
        
        $pdo->exec('CREATE TABLE IF NOT EXISTS comments (
            uuid TEXT PRIMARY KEY NOT NULL,
            posts_uuid TEXT NOT NULL,
            author_uuid TEXT NOT NULL,
            text TEXT NOT NULL
        )');
        
        $pdo->exec('CREATE TABLE IF NOT EXISTS post_likes (
            uuid TEXT PRIMARY KEY NOT NULL,
            post_uuid TEXT NOT NULL,
            user_uuid TEXT NOT NULL,
            UNIQUE(post_uuid, user_uuid)
        )');
        
        $pdo->exec('CREATE TABLE IF NOT EXISTS comment_likes (
            uuid TEXT PRIMARY KEY NOT NULL,
            comment_uuid TEXT NOT NULL,
            user_uuid TEXT NOT NULL,
            UNIQUE(comment_uuid, user_uuid)
        )');

        $this->pdo = $pdo;
        $this->logger = new TestLogger();
    }

    public function tearDown(): void
    {
        $this->pdo = null;
        $this->logger = null;
        gc_collect_cycles();
        
        if (file_exists($this->testDbPath)) {
            unlink($this->testDbPath);
        }
    }

    public function testUsersRepositorySaveLogsInfo(): void
    {
        $this->setUp();
        $repository = new UsersRepository($this->pdo, $this->logger);
        
        $userUuid = UUID::generate();
        $user = new User($userUuid, 'test_user', 'Test', 'User');
        $repository->save($user);
        
        $logs = $this->logger->getLogsByLevel('INFO');
        if (empty($logs)) {
            throw new Exception('Should log INFO message when saving user');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $userUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('INFO log should contain user UUID');
        }
        
        $this->tearDown();
    }

    public function testUsersRepositoryGetNotFoundLogsWarning(): void
    {
        $this->setUp();
        $repository = new UsersRepository($this->pdo, $this->logger);
        
        $nonExistentUuid = UUID::generate();
        try {
            $repository->get($nonExistentUuid);
        } catch (UserNotFoundException $e) {
            // Ожидаемое исключение
        }
        
        $logs = $this->logger->getLogsByLevel('WARNING');
        if (empty($logs)) {
            throw new Exception('Should log WARNING message when user not found');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $nonExistentUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('WARNING log should contain user UUID');
        }
        
        $this->tearDown();
    }

    public function testPostsRepositorySaveLogsInfo(): void
    {
        $this->setUp();
        
        // Создаем пользователя для статьи
        $userUuid = UUID::generate();
        $user = new User($userUuid, 'test_author', 'Test', 'Author');
        $usersRepo = new UsersRepository($this->pdo, $this->logger);
        $usersRepo->save($user);
        
        $this->logger->clearLogs();
        
        $postsRepo = new PostsRepository($this->pdo, $this->logger);
        $postUuid = UUID::generate();
        $post = new Post($postUuid, $userUuid, 'Test Post', 'Test content');
        $postsRepo->save($post);
        
        $logs = $this->logger->getLogsByLevel('INFO');
        if (empty($logs)) {
            throw new Exception('Should log INFO message when saving post');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $postUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('INFO log should contain post UUID');
        }
        
        $this->tearDown();
    }

    public function testPostsRepositoryGetNotFoundLogsWarning(): void
    {
        $this->setUp();
        $repository = new PostsRepository($this->pdo, $this->logger);
        
        $nonExistentUuid = UUID::generate();
        try {
            $repository->get($nonExistentUuid);
        } catch (PostNotFoundException $e) {
            // Ожидаемое исключение
        }
        
        $logs = $this->logger->getLogsByLevel('WARNING');
        if (empty($logs)) {
            throw new Exception('Should log WARNING message when post not found');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $nonExistentUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('WARNING log should contain post UUID');
        }
        
        $this->tearDown();
    }

    public function testCommentsRepositorySaveLogsInfo(): void
    {
        $this->setUp();
        
        // Создаем пользователя и статью для комментария
        $userUuid = UUID::generate();
        $user = new User($userUuid, 'test_user', 'Test', 'User');
        $usersRepo = new UsersRepository($this->pdo, $this->logger);
        $usersRepo->save($user);
        
        $postUuid = UUID::generate();
        $post = new Post($postUuid, $userUuid, 'Test Post', 'Test content');
        $postsRepo = new PostsRepository($this->pdo, $this->logger);
        $postsRepo->save($post);
        
        $this->logger->clearLogs();
        
        $commentsRepo = new CommentsRepository($this->pdo, $this->logger);
        $commentUuid = UUID::generate();
        $comment = new Comment($commentUuid, $postUuid, $userUuid, 'Test comment');
        $commentsRepo->save($comment);
        
        $logs = $this->logger->getLogsByLevel('INFO');
        if (empty($logs)) {
            throw new Exception('Should log INFO message when saving comment');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $commentUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('INFO log should contain comment UUID');
        }
        
        $this->tearDown();
    }

    public function testCommentsRepositoryGetNotFoundLogsWarning(): void
    {
        $this->setUp();
        $repository = new CommentsRepository($this->pdo, $this->logger);
        
        $nonExistentUuid = UUID::generate();
        try {
            $repository->get($nonExistentUuid);
        } catch (CommentNotFoundException $e) {
            // Ожидаемое исключение
        }
        
        $logs = $this->logger->getLogsByLevel('WARNING');
        if (empty($logs)) {
            throw new Exception('Should log WARNING message when comment not found');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $nonExistentUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('WARNING log should contain comment UUID');
        }
        
        $this->tearDown();
    }

    public function testLikesRepositorySaveLogsInfo(): void
    {
        $this->setUp();
        
        // Создаем пользователя и статью для лайка
        $userUuid = UUID::generate();
        $user = new User($userUuid, 'test_user', 'Test', 'User');
        $usersRepo = new UsersRepository($this->pdo, $this->logger);
        $usersRepo->save($user);
        
        $postUuid = UUID::generate();
        $post = new Post($postUuid, $userUuid, 'Test Post', 'Test content');
        $postsRepo = new PostsRepository($this->pdo, $this->logger);
        $postsRepo->save($post);
        
        $this->logger->clearLogs();
        
        $likesRepo = new LikesRepository($this->pdo, $this->logger);
        $likeUuid = UUID::generate();
        $like = new Like($likeUuid, $postUuid, $userUuid);
        $likesRepo->save($like);
        
        $logs = $this->logger->getLogsByLevel('INFO');
        if (empty($logs)) {
            throw new Exception('Should log INFO message when saving post like');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $likeUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('INFO log should contain like UUID');
        }
        
        $this->tearDown();
    }

    public function testCommentLikesRepositorySaveLogsInfo(): void
    {
        $this->setUp();
        
        // Создаем пользователя, статью и комментарий для лайка
        $userUuid = UUID::generate();
        $user = new User($userUuid, 'test_user', 'Test', 'User');
        $usersRepo = new UsersRepository($this->pdo, $this->logger);
        $usersRepo->save($user);
        
        $postUuid = UUID::generate();
        $post = new Post($postUuid, $userUuid, 'Test Post', 'Test content');
        $postsRepo = new PostsRepository($this->pdo, $this->logger);
        $postsRepo->save($post);
        
        $commentUuid = UUID::generate();
        $comment = new Comment($commentUuid, $postUuid, $userUuid, 'Test comment');
        $commentsRepo = new CommentsRepository($this->pdo, $this->logger);
        $commentsRepo->save($comment);
        
        $this->logger->clearLogs();
        
        $commentLikesRepo = new CommentLikesRepository($this->pdo, $this->logger);
        $likeUuid = UUID::generate();
        $like = new CommentLike($likeUuid, $commentUuid, $userUuid);
        $commentLikesRepo->save($like);
        
        $logs = $this->logger->getLogsByLevel('INFO');
        if (empty($logs)) {
            throw new Exception('Should log INFO message when saving comment like');
        }
        
        $found = false;
        foreach ($logs as $log) {
            if (strpos($log['message'], $likeUuid->getValue()) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception('INFO log should contain comment like UUID');
        }
        
        $this->tearDown();
    }
}

