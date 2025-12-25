<?php

namespace App\Models;

class Post
{
    private int $id;
    private int $authorId;
    private string $title;
    private string $text;

    public function __construct(int $id, int $authorId, string $title, string $text)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->text = $text;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setAuthorId(int $authorId): void
    {
        $this->authorId = $authorId;
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

