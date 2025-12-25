<?php

require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Utils/Arguments.php';

class User
{
    private UUID $uuid;
    private string $username;
    private string $firstName;
    private string $lastName;

    public function __construct(
        UUID $uuid,
        string $username,
        string $firstName,
        string $lastName
    ) {
        Arguments::stringNotEmpty($username, 'Username');
        Arguments::stringNotEmpty($firstName, 'First name');
        Arguments::stringNotEmpty($lastName, 'Last name');

        $this->uuid = $uuid;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}

