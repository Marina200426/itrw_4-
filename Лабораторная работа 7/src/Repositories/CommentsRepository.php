<?php

require_once __DIR__ . '/CommentsRepositoryInterface.php';
require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Exceptions/CommentNotFoundException.php';

class CommentsRepository implements CommentsRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(UUID $uuid): Comment
    {
        $stmt = $this->pdo->prepare('SELECT uuid, posts_uuid, author_uuid, text FROM comments WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid->getValue()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new CommentNotFoundException($uuid->getValue());
        }

        return new Comment(
            new UUID($row['uuid']),
            new UUID($row['posts_uuid']),
            new UUID($row['author_uuid']),
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
            'uuid' => $comment->getUuid()->getValue(),
            'posts_uuid' => $comment->getPostsUuid()->getValue(),
            'author_uuid' => $comment->getAuthorUuid()->getValue(),
            'text' => $comment->getText()
        ]);
    }

    public function getByPostUuid(UUID $postUuid): array
    {
        $stmt = $this->pdo->prepare('SELECT uuid, posts_uuid, author_uuid, text FROM comments WHERE posts_uuid = :posts_uuid');
        $stmt->execute(['posts_uuid' => $postUuid->getValue()]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $comments = [];
        foreach ($rows as $row) {
            $comments[] = new Comment(
                new UUID($row['uuid']),
                new UUID($row['posts_uuid']),
                new UUID($row['author_uuid']),
                $row['text']
            );
        }

        return $comments;
    }

    public function deleteByPostUuid(UUID $postUuid): int
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE posts_uuid = :posts_uuid');
        $stmt->execute(['posts_uuid' => $postUuid->getValue()]);
        return $stmt->rowCount();
    }

    public function delete(UUID $uuid): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid->getValue()]);
        
        if ($stmt->rowCount() === 0) {
            throw new CommentNotFoundException($uuid->getValue());
        }
    }
}


