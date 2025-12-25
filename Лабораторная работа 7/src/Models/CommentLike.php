<?php

require_once __DIR__ . '/../Utils/UUID.php';

class CommentLike
{
    private UUID $uuid;
    private UUID $commentUuid;
    private UUID $userUuid;

    public function __construct(
        UUID $uuid,
        UUID $commentUuid,
        UUID $userUuid
    ) {
        $this->uuid = $uuid;
        $this->commentUuid = $commentUuid;
        $this->userUuid = $userUuid;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getCommentUuid(): UUID
    {
        return $this->commentUuid;
    }

    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }
}


