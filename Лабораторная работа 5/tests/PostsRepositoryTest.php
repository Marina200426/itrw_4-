<?php

require_once __DIR__ . '/../src/Utils/UUID.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Repositories/PostsRepository.php';
require_once __DIR__ . '/../src/Exceptions/PostNotFoundException.php';
require_once __DIR__ . '/../config/database.php';

class PostsRepositoryTest
{
    private ?PDO $pdo = null;
    private ?PostsRepository $repository = null;
    private ?string $testDbPath = null;

    public function setUp(): void
    {
        // Создаем временную базу данных для тестов
        $this->testDbPath = sys_get_temp_dir() . '/test_posts_' . uniqid() . '.db';
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

        $this->pdo = $pdo;
        $this->repository = new PostsRepository($pdo);
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

    public function testSavePost(): void
    {
        $this->setUp();
        
        $authorUuid = UUID::generate();
        $postUuid = UUID::generate();
        
        // Создаем пользователя для теста
        $stmt = $this->pdo->prepare('INSERT INTO users (uuid, username, first_name, last_name) VALUES (?, ?, ?, ?)');
        $stmt->execute([$authorUuid->getValue(), 'testuser', 'Test', 'User']);
        
        $post = new Post($postUuid, $authorUuid, 'Test Title', 'Test Text');
        $this->repository->save($post);
        
        // Проверяем, что статья сохранена
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE uuid = ?');
        $stmt->execute([$postUuid->getValue()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false || $row['title'] !== 'Test Title' || $row['text'] !== 'Test Text') {
            throw new Exception('Post should be saved in database with correct data');
        }
        
        $this->tearDown();
    }

    public function testGetPostByUuid(): void
    {
        $this->setUp();
        
        $authorUuid = UUID::generate();
        $postUuid = UUID::generate();
        
        // Создаем пользователя
        $stmt = $this->pdo->prepare('INSERT INTO users (uuid, username, first_name, last_name) VALUES (?, ?, ?, ?)');
        $stmt->execute([$authorUuid->getValue(), 'testuser', 'Test', 'User']);
        
        // Сохраняем статью
        $post = new Post($postUuid, $authorUuid, 'Test Title', 'Test Text');
        $this->repository->save($post);
        
        // Получаем статью
        $retrievedPost = $this->repository->get($postUuid);
        
        if (!($retrievedPost instanceof Post) || 
            !$retrievedPost->getUuid()->equals($postUuid) || 
            $retrievedPost->getTitle() !== 'Test Title' || 
            $retrievedPost->getText() !== 'Test Text') {
            throw new Exception('Retrieved post should match saved post');
        }
        
        $this->tearDown();
    }

    public function testGetPostThrowsExceptionWhenNotFound(): void
    {
        $this->setUp();
        
        $nonExistentUuid = UUID::generate();
        
        $exceptionThrown = false;
        try {
            $this->repository->get($nonExistentUuid);
        } catch (PostNotFoundException $e) {
            $exceptionThrown = true;
            if (strpos($e->getMessage(), $nonExistentUuid->getValue()) === false) {
                throw new Exception('Exception message should contain UUID');
            }
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw PostNotFoundException');
        }
        
        $this->tearDown();
    }
}

