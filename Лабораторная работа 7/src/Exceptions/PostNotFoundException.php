<?php

class PostNotFoundException extends Exception
{
    public function __construct(string $uuid)
    {
        parent::__construct("Post with UUID '{$uuid}' not found", 404);
    }
}


