<?php

interface PostsRepositoryInterface
{
    /**
     * Получить статью по UUID
     * @param string $uuid UUID статьи
     * @return Post|null Объект статьи или null, если не найдена
     */
    public function get(string $uuid): ?Post;

    /**
     * Сохранить статью
     * @param Post $post Объект статьи для сохранения
     * @return void
     */
    public function save(Post $post): void;
}

