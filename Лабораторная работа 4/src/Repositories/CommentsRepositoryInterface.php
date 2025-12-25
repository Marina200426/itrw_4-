<?php

interface CommentsRepositoryInterface
{
    /**
     * Получить комментарий по UUID
     * @param string $uuid UUID комментария
     * @return Comment|null Объект комментария или null, если не найден
     */
    public function get(string $uuid): ?Comment;

    /**
     * Сохранить комментарий
     * @param Comment $comment Объект комментария для сохранения
     * @return void
     */
    public function save(Comment $comment): void;
}

