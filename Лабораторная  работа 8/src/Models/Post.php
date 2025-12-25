<?php

require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Utils/Arguments.php';

class Post
{
    private UUID $uuid;
    private UUID $authorUuid;
    private string $title;
    private string $text;

    public function __construct(
        UUID $uuid,
        UUID $authorUuid,
        string $title,
        string $text
    ) {
        Arguments::stringNotEmpty($title, 'Title');
        Arguments::stringNotEmpty($text, 'Text');

        $this->uuid = $uuid;
        $this->authorUuid = $authorUuid;
        $this->title = $title;
        $this->text = $text;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getAuthorUuid(): UUID
    {
        return $this->authorUuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }
}

