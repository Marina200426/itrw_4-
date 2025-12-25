<?php

require_once __DIR__ . '/CommentLikesRepositoryInterface.php';
require_once __DIR__ . '/../Models/CommentLike.php';
require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Logger/LoggerInterface.php';

class CommentLikesRepository implements CommentLikesRepositoryInterface
{
    private PDO $pdo;
    private LoggerInterface $logger;

    public function __construct(PDO $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function save(CommentLike $like): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO comment_likes (uuid, comment_uuid, user_uuid) 
             VALUES (:uuid, :comment_uuid, :user_uuid)'
        );
        $stmt->execute([
            'uuid' => $like->getUuid()->getValue(),
            'comment_uuid' => $like->getCommentUuid()->getValue(),
            'user_uuid' => $like->getUserUuid()->getValue()
        ]);
        
        $this->logger->info("Comment like saved: {$like->getUuid()->getValue()}");
    }

    public function getByCommentUuid(UUID $commentUuid): array
    {
        $stmt = $this->pdo->prepare('SELECT uuid, comment_uuid, user_uuid FROM comment_likes WHERE comment_uuid = :comment_uuid');
        $stmt->execute(['comment_uuid' => $commentUuid->getValue()]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $likes = [];
        foreach ($rows as $row) {
            $likes[] = new CommentLike(
                new UUID($row['uuid']),
                new UUID($row['comment_uuid']),
                new UUID($row['user_uuid'])
            );
        }

        return $likes;
    }

    public function exists(UUID $commentUuid, UUID $userUuid): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM comment_likes WHERE comment_uuid = :comment_uuid AND user_uuid = :user_uuid'
        );
        $stmt->execute([
            'comment_uuid' => $commentUuid->getValue(),
            'user_uuid' => $userUuid->getValue()
        ]);

        return $stmt->fetchColumn() > 0;
    }
}

