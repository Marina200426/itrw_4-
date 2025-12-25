<?php

require_once __DIR__ . '/../Models/CommentLike.php';
require_once __DIR__ . '/../Utils/UUID.php';

interface CommentLikesRepositoryInterface
{
    public function save(CommentLike $like): void;
    public function getByCommentUuid(UUID $commentUuid): array;
    public function exists(UUID $commentUuid, UUID $userUuid): bool;
}


