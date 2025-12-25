<?php

require_once __DIR__ . '/../src/Utils/UUID.php';
require_once __DIR__ . '/../src/Models/Comment.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Repositories/CommentsRepository.php';
require_once __DIR__ . '/../src/Exceptions/CommentNotFoundException.php';
require_once __DIR__ . '/../config/database.php';

class CommentsRepositoryTest
{
    private ?PDO $pdo = null;
    private ?CommentsRepository $repository = null;
    private ?string $testDbPath = null;

    public function setUp(): void
    {
        // Создаем временную базу данных для тестов
        $this->testDbPath = sys_get_temp_dir() . '/test_comments_' . uniqid() . '.db';
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

        $this->pdo = $pdo;
        $this->repository = new CommentsRepository($pdo);
    }

    public function tearDown(): void
    {
        // Закрываем соединение с базой данных
        $this->repository = null;
        $this->pdo = null;
        
        // Принудительная сборка мусора для освобождения ресурсов
        gc_collect_cycles();
        
        // Небольшая задержка для освобождения ресурсов (особенно важно для Windows)
        usleep(50000); // 50ms
        
        // Удаляем временную базу данных с обработкой ошибок
        if ($this->testDbPath !== null && file_exists($this->testDbPath)) {
            $attempts = 0;
            $maxAttempts = 5;
            while ($attempts < $maxAttempts) {
                if (@unlink($this->testDbPath)) {
                    break;
                }
                $attempts++;
                usleep(100000); // 100ms между попытками
            }
        }
        $this->testDbPath = null;
    }

    public function testSaveComment(): void
    {
        $this->setUp();
        
        $authorUuid = UUID::generate();
        $postUuid = UUID::generate();
        $commentUuid = UUID::generate();
        
        // Создаем пользователя и статью
        $stmt = $this->pdo->prepare('INSERT INTO users (uuid, username, first_name, last_name) VALUES (?, ?, ?, ?)');
        $stmt->execute([$authorUuid->getValue(), 'testuser', 'Test', 'User']);
        
        $stmt = $this->pdo->prepare('INSERT INTO posts (uuid, author_uuid, title, text) VALUES (?, ?, ?, ?)');
        $stmt->execute([$postUuid->getValue(), $authorUuid->getValue(), 'Test Post', 'Post Text']);
        
        $comment = new Comment($commentUuid, $postUuid, $authorUuid, 'Test Comment');
        $this->repository->save($comment);
        
        // Проверяем, что комментарий сохранен
        $stmt = $this->pdo->prepare('SELECT * FROM comments WHERE uuid = ?');
        $stmt->execute([$commentUuid->getValue()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false || $row['text'] !== 'Test Comment') {
            throw new Exception('Comment should be saved in database with correct data');
        }
        
        $this->tearDown();
    }

    public function testGetCommentByUuid(): void
    {
        $this->setUp();
        
        $authorUuid = UUID::generate();
        $postUuid = UUID::generate();
        $commentUuid = UUID::generate();
        
        // Создаем пользователя и статью
        $stmt = $this->pdo->prepare('INSERT INTO users (uuid, username, first_name, last_name) VALUES (?, ?, ?, ?)');
        $stmt->execute([$authorUuid->getValue(), 'testuser', 'Test', 'User']);
        
        $stmt = $this->pdo->prepare('INSERT INTO posts (uuid, author_uuid, title, text) VALUES (?, ?, ?, ?)');
        $stmt->execute([$postUuid->getValue(), $authorUuid->getValue(), 'Test Post', 'Post Text']);
        
        // Сохраняем комментарий
        $comment = new Comment($commentUuid, $postUuid, $authorUuid, 'Test Comment');
        $this->repository->save($comment);
        
        // Получаем комментарий
        $retrievedComment = $this->repository->get($commentUuid);
        
        if (!($retrievedComment instanceof Comment) || 
            !$retrievedComment->getUuid()->equals($commentUuid) || 
            $retrievedComment->getText() !== 'Test Comment') {
            throw new Exception('Retrieved comment should match saved comment');
        }
        
        $this->tearDown();
    }

    public function testGetCommentThrowsExceptionWhenNotFound(): void
    {
        $this->setUp();
        
        $nonExistentUuid = UUID::generate();
        
        $exceptionThrown = false;
        try {
            $this->repository->get($nonExistentUuid);
        } catch (CommentNotFoundException $e) {
            $exceptionThrown = true;
            if (strpos($e->getMessage(), $nonExistentUuid->getValue()) === false) {
                throw new Exception('Exception message should contain UUID');
            }
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw CommentNotFoundException');
        }
        
        $this->tearDown();
    }
}

