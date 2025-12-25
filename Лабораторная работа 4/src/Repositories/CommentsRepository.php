<?php

require_once __DIR__ . '/CommentsRepositoryInterface.php';
require_once __DIR__ . '/../Models/Comment.php';

class CommentsRepository implements CommentsRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(string $uuid): ?Comment
    {
        $stmt = $this->pdo->prepare('SELECT uuid, posts_uuid, author_uuid, text FROM comments WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Comment(
            $row['uuid'],
            $row['posts_uuid'],
            $row['author_uuid'],
            $row['text']
        );
    }

    public function save(Comment $comment): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT OR REPLACE INTO comments (uuid, posts_uuid, author_uuid, text) 
             VALUES (:uuid, :posts_uuid, :author_uuid, :text)'
        );
        $stmt->execute([
            'uuid' => $comment->getUuid(),
            'posts_uuid' => $comment->getPostsUuid(),
            'author_uuid' => $comment->getAuthorUuid(),
            'text' => $comment->getText()
        ]);
    }
}

