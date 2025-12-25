<?php

require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Utils/Arguments.php';

class Comment
{
    private UUID $uuid;
    private UUID $postsUuid;
    private UUID $authorUuid;
    private string $text;

    public function __construct(
        UUID $uuid,
        UUID $postsUuid,
        UUID $authorUuid,
        string $text
    ) {
        Arguments::stringNotEmpty($text, 'Text');

        $this->uuid = $uuid;
        $this->postsUuid = $postsUuid;
        $this->authorUuid = $authorUuid;
        $this->text = $text;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getPostsUuid(): UUID
    {
        return $this->postsUuid;
    }

    public function getAuthorUuid(): UUID
    {
        return $this->authorUuid;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setPostsUuid(UUID $postsUuid): void
    {
        $this->postsUuid = $postsUuid;
    }

    public function setAuthorUuid(UUID $authorUuid): void
    {
        $this->authorUuid = $authorUuid;
    }

    public function setText(string $text): void
    {
        Arguments::stringNotEmpty($text, 'Text');
        $this->text = $text;
    }
}

