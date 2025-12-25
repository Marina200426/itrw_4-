<?php

class UserNotFoundException extends Exception
{
    public function __construct(string $uuid)
    {
        parent::__construct("User with UUID '{$uuid}' not found", 404);
    }
}

