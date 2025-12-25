<?php

require_once __DIR__ . '/../Models/Like.php';
require_once __DIR__ . '/../Utils/UUID.php';

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $postUuid): array;
    public function exists(UUID $postUuid, UUID $userUuid): bool;
}


