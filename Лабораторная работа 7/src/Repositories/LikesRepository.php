<?php

require_once __DIR__ . '/LikesRepositoryInterface.php';
require_once __DIR__ . '/../Models/Like.php';
require_once __DIR__ . '/../Utils/UUID.php';

class LikesRepository implements LikesRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Like $like): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO post_likes (uuid, post_uuid, user_uuid) 
             VALUES (:uuid, :post_uuid, :user_uuid)'
        );
        $stmt->execute([
            'uuid' => $like->getUuid()->getValue(),
            'post_uuid' => $like->getPostUuid()->getValue(),
            'user_uuid' => $like->getUserUuid()->getValue()
        ]);
    }

    public function getByPostUuid(UUID $postUuid): array
    {
        $stmt = $this->pdo->prepare('SELECT uuid, post_uuid, user_uuid FROM post_likes WHERE post_uuid = :post_uuid');
        $stmt->execute(['post_uuid' => $postUuid->getValue()]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $likes = [];
        foreach ($rows as $row) {
            $likes[] = new Like(
                new UUID($row['uuid']),
                new UUID($row['post_uuid']),
                new UUID($row['user_uuid'])
            );
        }

        return $likes;
    }

    public function exists(UUID $postUuid, UUID $userUuid): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM post_likes WHERE post_uuid = :post_uuid AND user_uuid = :user_uuid'
        );
        $stmt->execute([
            'post_uuid' => $postUuid->getValue(),
            'user_uuid' => $userUuid->getValue()
        ]);

        return $stmt->fetchColumn() > 0;
    }
}


