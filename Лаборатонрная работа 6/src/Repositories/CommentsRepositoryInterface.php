<?php

require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Utils/UUID.php';

interface CommentsRepositoryInterface
{
    public function get(UUID $uuid): Comment;
    public function save(Comment $comment): void;
    public function getByPostUuid(UUID $postUuid): array;
    public function deleteByPostUuid(UUID $postUuid): int;
    public function delete(UUID $uuid): void;
}

