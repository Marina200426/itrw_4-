<?php

require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Utils/UUID.php';

interface PostsRepositoryInterface
{
    /**
     * Получить статью по UUID
     * @param UUID $uuid UUID статьи
     * @return Post Объект статьи
     * @throws PostNotFoundException Если статья не найдена
     */
    public function get(UUID $uuid): Post;

    /**
     * Сохранить статью
     * @param Post $post Объект статьи для сохранения
     * @return void
     */
    public function save(Post $post): void;
}

