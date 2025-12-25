<?php

require_once __DIR__ . '/PostsRepositoryInterface.php';
require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Exceptions/PostNotFoundException.php';

class PostsRepository implements PostsRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(UUID $uuid): Post
    {
        $stmt = $this->pdo->prepare('SELECT uuid, author_uuid, title, text FROM posts WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid->getValue()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new PostNotFoundException($uuid->getValue());
        }

        return new Post(
            new UUID($row['uuid']),
            new UUID($row['author_uuid']),
            $row['title'],
            $row['text']
        );
    }

    public function save(Post $post): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT OR REPLACE INTO posts (uuid, author_uuid, title, text) 
             VALUES (:uuid, :author_uuid, :title, :text)'
        );
        $stmt->execute([
            'uuid' => $post->getUuid()->getValue(),
            'author_uuid' => $post->getAuthorUuid()->getValue(),
            'title' => $post->getTitle(),
            'text' => $post->getText()
        ]);
    }

    public function delete(UUID $uuid): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid->getValue()]);
        
        if ($stmt->rowCount() === 0) {
            throw new PostNotFoundException($uuid->getValue());
        }
    }
}


