<?php

class Comment
{
    private string $uuid;
    private string $postsUuid;
    private string $authorUuid;
    private string $text;

    public function __construct(
        string $uuid,
        string $postsUuid,
        string $authorUuid,
        string $text
    ) {
        $this->uuid = $uuid;
        $this->postsUuid = $postsUuid;
        $this->authorUuid = $authorUuid;
        $this->text = $text;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPostsUuid(): string
    {
        return $this->postsUuid;
    }

    public function getAuthorUuid(): string
    {
        return $this->authorUuid;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setPostsUuid(string $postsUuid): void
    {
        $this->postsUuid = $postsUuid;
    }

    public function setAuthorUuid(string $authorUuid): void
    {
        $this->authorUuid = $authorUuid;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}

