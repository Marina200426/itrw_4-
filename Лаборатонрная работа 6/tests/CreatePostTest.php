<?php

require_once __DIR__ . '/../src/Services/CreatePost.php';
require_once __DIR__ . '/../src/Utils/UUID.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Repositories/UsersRepository.php';
require_once __DIR__ . '/../src/Repositories/PostsRepository.php';
require_once __DIR__ . '/../src/Exceptions/UserNotFoundException.php';
require_once __DIR__ . '/../config/database.php';

class CreatePostTest
{
    private ?PDO $pdo = null;
    private ?UsersRepository $usersRepository = null;
    private ?PostsRepository $postsRepository = null;
    private ?CreatePost $createPost = null;
    private ?string $testDbPath = null;

    public function setUp(): void
    {
        // Создаем временную базу данных для тестов
        $this->testDbPath = sys_get_temp_dir() . '/test_createpost_' . uniqid() . '.db';
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
        $this->usersRepository = new UsersRepository($pdo);
        $this->postsRepository = new PostsRepository($pdo);
        $this->createPost = new CreatePost($this->usersRepository, $this->postsRepository);
    }

    public function tearDown(): void
    {
        $this->createPost = null;
        $this->postsRepository = null;
        $this->usersRepository = null;
        $this->pdo = null;
        
        gc_collect_cycles();
        usleep(50000);
        
        if ($this->testDbPath !== null && file_exists($this->testDbPath)) {
            $attempts = 0;
            $maxAttempts = 5;
            while ($attempts < $maxAttempts) {
                if (@unlink($this->testDbPath)) {
                    break;
                }
                $attempts++;
                usleep(100000);
            }
        }
        $this->testDbPath = null;
    }

    public function testCreatePostReturnsSuccess(): void
    {
        $this->setUp();
        
        // Создаем пользователя
        $authorUuid = UUID::generate();
        $user = new User($authorUuid, 'testuser', 'Test', 'User');
        $this->usersRepository->save($user);
        
        // Данные для создания статьи
        $data = [
            'author_uuid' => $authorUuid->getValue(),
            'title' => 'Test Post',
            'text' => 'This is a test post'
        ];
        
        $result = $this->createPost->execute($data);
        
        if (!$result['success']) {
            throw new Exception('Expected success but got: ' . $result['message']);
        }
        
        if (!isset($result['data']['uuid']) || !isset($result['data']['title'])) {
            throw new Exception('Result should contain post data');
        }
        
        // Проверяем, что статья сохранена в БД
        $savedPost = $this->postsRepository->get(new UUID($result['data']['uuid']));
        if ($savedPost->getTitle() !== 'Test Post') {
            throw new Exception('Post title should match');
        }
        
        $this->tearDown();
    }

    public function testCreatePostReturnsErrorForInvalidUUID(): void
    {
        $this->setUp();
        
        $data = [
            'author_uuid' => 'invalid-uuid-format',
            'title' => 'Test Post',
            'text' => 'This is a test post'
        ];
        
        $result = $this->createPost->execute($data);
        
        if ($result['success']) {
            throw new Exception('Expected error for invalid UUID');
        }
        
        if (strpos($result['message'], 'Invalid UUID format') === false) {
            throw new Exception('Error message should mention invalid UUID format');
        }
        
        $this->tearDown();
    }

    public function testCreatePostReturnsErrorForNonExistentUser(): void
    {
        $this->setUp();
        
        $nonExistentUuid = UUID::generate();
        
        $data = [
            'author_uuid' => $nonExistentUuid->getValue(),
            'title' => 'Test Post',
            'text' => 'This is a test post'
        ];
        
        $result = $this->createPost->execute($data);
        
        if ($result['success']) {
            throw new Exception('Expected error for non-existent user');
        }
        
        if (strpos($result['message'], 'User not found') === false) {
            throw new Exception('Error message should mention user not found');
        }
        
        $this->tearDown();
    }

    public function testCreatePostReturnsErrorForMissingFields(): void
    {
        $this->setUp();
        
        // Тест без author_uuid
        $data1 = [
            'title' => 'Test Post',
            'text' => 'This is a test post'
        ];
        $result1 = $this->createPost->execute($data1);
        if ($result1['success'] || strpos($result1['message'], 'Missing required fields') === false) {
            throw new Exception('Should return error for missing author_uuid');
        }
        
        // Тест без title
        $authorUuid = UUID::generate();
        $user = new User($authorUuid, 'testuser', 'Test', 'User');
        $this->usersRepository->save($user);
        
        $data2 = [
            'author_uuid' => $authorUuid->getValue(),
            'text' => 'This is a test post'
        ];
        $result2 = $this->createPost->execute($data2);
        if ($result2['success'] || strpos($result2['message'], 'Missing required fields') === false) {
            throw new Exception('Should return error for missing title');
        }
        
        // Тест без text
        $data3 = [
            'author_uuid' => $authorUuid->getValue(),
            'title' => 'Test Post'
        ];
        $result3 = $this->createPost->execute($data3);
        if ($result3['success'] || strpos($result3['message'], 'Missing required fields') === false) {
            throw new Exception('Should return error for missing text');
        }
        
        $this->tearDown();
    }
}

