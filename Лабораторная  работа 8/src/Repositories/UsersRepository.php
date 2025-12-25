<?php

require_once __DIR__ . '/UsersRepositoryInterface.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Exceptions/UserNotFoundException.php';
require_once __DIR__ . '/../Logger/LoggerInterface.php';

class UsersRepository implements UsersRepositoryInterface
{
    private PDO $pdo;
    private LoggerInterface $logger;

    public function __construct(PDO $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function get(UUID $uuid): User
    {
        $stmt = $this->pdo->prepare('SELECT uuid, username, first_name, last_name FROM users WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid->getValue()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $this->logger->warning("User not found: {$uuid->getValue()}");
            throw new UserNotFoundException($uuid->getValue());
        }

        return new User(
            new UUID($row['uuid']),
            $row['username'],
            $row['first_name'],
            $row['last_name']
        );
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT OR REPLACE INTO users (uuid, username, first_name, last_name) 
             VALUES (:uuid, :username, :first_name, :last_name)'
        );
        $stmt->execute([
            'uuid' => $user->getUuid()->getValue(),
            'username' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName()
        ]);
        
        $this->logger->info("User saved: {$user->getUuid()->getValue()}");
    }
}

