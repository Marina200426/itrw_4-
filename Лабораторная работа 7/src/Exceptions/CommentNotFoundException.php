<?php

class CommentNotFoundException extends Exception
{
    public function __construct(string $uuid)
    {
        parent::__construct("Comment with UUID '{$uuid}' not found", 404);
    }
}


