<?php

require_once __DIR__ . '/../Utils/UUID.php';

class Like
{
    private UUID $uuid;
    private UUID $postUuid;
    private UUID $userUuid;

    public function __construct(
        UUID $uuid,
        UUID $postUuid,
        UUID $userUuid
    ) {
        $this->uuid = $uuid;
        $this->postUuid = $postUuid;
        $this->userUuid = $userUuid;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getPostUuid(): UUID
    {
        return $this->postUuid;
    }

    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }
}


