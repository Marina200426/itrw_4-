<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Utils/UUID.php';

interface UsersRepositoryInterface
{
    public function get(UUID $uuid): User;
    public function save(User $user): void;
}


