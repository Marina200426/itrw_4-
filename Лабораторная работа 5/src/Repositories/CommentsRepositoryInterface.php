<?php

require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Utils/UUID.php';

interface CommentsRepositoryInterface
{
    /**
     * Получить комментарий по UUID
     * @param UUID $uuid UUID комментария
     * @return Comment Объект комментария
     * @throws CommentNotFoundException Если комментарий не найден
     */
    public function get(UUID $uuid): Comment;

    /**
     * Сохранить комментарий
     * @param Comment $comment Объект комментария для сохранения
     * @return void
     */
    public function save(Comment $comment): void;
}

