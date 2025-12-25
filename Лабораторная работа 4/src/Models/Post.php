<?php

class Post
{
    private string $uuid;
    private string $authorUuid;
    private string $title;
    private string $text;

    public function __construct(
        string $uuid,
        string $authorUuid,
        string $title,
        string $text
    ) {
        $this->uuid = $uuid;
        $this->authorUuid = $authorUuid;
        $this->title = $title;
        $this->text = $text;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getAuthorUuid(): string
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

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setAuthorUuid(string $authorUuid): void
    {
        $this->authorUuid = $authorUuid;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}

