<?php

require_once __DIR__ . '/../src/Utils/UUID.php';
require_once __DIR__ . '/../src/Utils/Arguments.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Models/Comment.php';

class ModelsTest
{
    public function testUUIDGeneration(): void
    {
        $uuid = UUID::generate();
        if (!($uuid instanceof UUID) || empty($uuid->getValue())) {
            throw new Exception('Should return valid UUID instance');
        }
    }

    public function testUUIDValidation(): void
    {
        $validUuid = new UUID('550e8400-e29b-41d4-a716-446655440000');
        if ($validUuid->getValue() !== '550e8400-e29b-41d4-a716-446655440000') {
            throw new Exception('Valid UUID should be accepted');
        }
        
        $exceptionThrown = false;
        try {
            new UUID('invalid-uuid');
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw InvalidArgumentException for invalid UUID');
        }
    }

    public function testUUIDEquals(): void
    {
        $uuid1 = new UUID('550e8400-e29b-41d4-a716-446655440000');
        $uuid2 = new UUID('550e8400-e29b-41d4-a716-446655440000');
        $uuid3 = new UUID('660e8400-e29b-41d4-a716-446655440001');
        
        if (!$uuid1->equals($uuid2) || $uuid1->equals($uuid3)) {
            throw new Exception('UUID equals method should work correctly');
        }
    }

    public function testArgumentsNotEmpty(): void
    {
        $exceptionThrown = false;
        try {
            Arguments::notEmpty('', 'Test');
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw InvalidArgumentException for empty string');
        }
        
        Arguments::notEmpty('value', 'Test'); // Should not throw
    }

    public function testArgumentsNotNull(): void
    {
        $exceptionThrown = false;
        try {
            Arguments::notNull(null, 'Test');
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw InvalidArgumentException for null');
        }
        
        Arguments::notNull('value', 'Test'); // Should not throw
    }

    public function testArgumentsStringNotEmpty(): void
    {
        $exceptionThrown = false;
        try {
            Arguments::stringNotEmpty('', 'Test');
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw InvalidArgumentException for empty string');
        }
        
        Arguments::stringNotEmpty('value', 'Test'); // Should not throw
    }

    public function testUserCreation(): void
    {
        $uuid = UUID::generate();
        $user = new User($uuid, 'testuser', 'Test', 'User');
        
        if (!$user->getUuid()->equals($uuid) || 
            $user->getUsername() !== 'testuser' || 
            $user->getFirstName() !== 'Test' || 
            $user->getLastName() !== 'User') {
            throw new Exception('User should be created with correct values');
        }
    }

    public function testUserValidation(): void
    {
        $uuid = UUID::generate();
        
        $exceptionThrown = false;
        try {
            new User($uuid, '', 'Test', 'User');
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw InvalidArgumentException for empty username');
        }
    }

    public function testPostCreation(): void
    {
        $uuid = UUID::generate();
        $authorUuid = UUID::generate();
        $post = new Post($uuid, $authorUuid, 'Test Title', 'Test Text');
        
        if (!$post->getUuid()->equals($uuid) || 
            !$post->getAuthorUuid()->equals($authorUuid) || 
            $post->getTitle() !== 'Test Title' || 
            $post->getText() !== 'Test Text') {
            throw new Exception('Post should be created with correct values');
        }
    }

    public function testPostValidation(): void
    {
        $uuid = UUID::generate();
        $authorUuid = UUID::generate();
        
        $exceptionThrown = false;
        try {
            new Post($uuid, $authorUuid, '', 'Test Text');
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw InvalidArgumentException for empty title');
        }
    }

    public function testCommentCreation(): void
    {
        $uuid = UUID::generate();
        $postUuid = UUID::generate();
        $authorUuid = UUID::generate();
        $comment = new Comment($uuid, $postUuid, $authorUuid, 'Test Comment');
        
        if (!$comment->getUuid()->equals($uuid) || 
            !$comment->getPostsUuid()->equals($postUuid) || 
            !$comment->getAuthorUuid()->equals($authorUuid) || 
            $comment->getText() !== 'Test Comment') {
            throw new Exception('Comment should be created with correct values');
        }
    }

    public function testCommentValidation(): void
    {
        $uuid = UUID::generate();
        $postUuid = UUID::generate();
        $authorUuid = UUID::generate();
        
        $exceptionThrown = false;
        try {
            new Comment($uuid, $postUuid, $authorUuid, '');
        } catch (InvalidArgumentException $e) {
            $exceptionThrown = true;
        }
        if (!$exceptionThrown) {
            throw new Exception('Should throw InvalidArgumentException for empty text');
        }
    }
}

