<?php

namespace App\Models;

class Comment
{
    private int $id;
    private int $authorId;
    private int $postId;
    private string $text;

    public function __construct(int $id, int $authorId, int $postId, string $text)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->postId = $postId;
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

    public function getPostId(): int
    {
        return $this->postId;
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

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}

