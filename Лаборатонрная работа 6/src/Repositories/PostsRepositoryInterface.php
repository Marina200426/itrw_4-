<?php

require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Utils/UUID.php';

interface PostsRepositoryInterface
{
    public function get(UUID $uuid): Post;
    public function save(Post $post): void;
    public function delete(UUID $uuid): void;
}

