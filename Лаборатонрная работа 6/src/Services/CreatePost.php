<?php

require_once __DIR__ . '/../Repositories/UsersRepositoryInterface.php';
require_once __DIR__ . '/../Repositories/PostsRepositoryInterface.php';
require_once __DIR__ . '/../Utils/UUID.php';
require_once __DIR__ . '/../Utils/Arguments.php';
require_once __DIR__ . '/../Exceptions/UserNotFoundException.php';

class CreatePost
{
    private UsersRepositoryInterface $usersRepository;
    private PostsRepositoryInterface $postsRepository;

    public function __construct(
        UsersRepositoryInterface $usersRepository,
        PostsRepositoryInterface $postsRepository
    ) {
        $this->usersRepository = $usersRepository;
        $this->postsRepository = $postsRepository;
    }

    /**
     * Создает статью на основе данных запроса
     * @param array $data Данные запроса (author_uuid, title, text)
     * @return array Результат операции ['success' => bool, 'message' => string, 'data' => ?array]
     */
    public function execute(array $data): array
    {
        try {
            // Проверка наличия всех необходимых данных
            if (!isset($data['author_uuid']) || !isset($data['title']) || !isset($data['text'])) {
                return [
                    'success' => false,
                    'message' => 'Missing required fields: author_uuid, title, text',
                    'data' => null
                ];
            }

            // Валидация UUID
            try {
                $authorUuid = new UUID($data['author_uuid']);
            } catch (InvalidArgumentException $e) {
                return [
                    'success' => false,
                    'message' => 'Invalid UUID format: ' . $data['author_uuid'],
                    'data' => null
                ];
            }

            // Проверка существования пользователя
            try {
                $this->usersRepository->get($authorUuid);
            } catch (UserNotFoundException $e) {
                return [
                    'success' => false,
                    'message' => 'User not found: ' . $authorUuid->getValue(),
                    'data' => null
                ];
            }

            // Валидация title и text
            Arguments::stringNotEmpty($data['title'], 'Title');
            Arguments::stringNotEmpty($data['text'], 'Text');

            // Создание статьи
            $postUuid = UUID::generate();
            $post = new Post($postUuid, $authorUuid, $data['title'], $data['text']);
            $this->postsRepository->save($post);

            return [
                'success' => true,
                'message' => 'Post created successfully',
                'data' => [
                    'uuid' => $postUuid->getValue(),
                    'author_uuid' => $authorUuid->getValue(),
                    'title' => $data['title'],
                    'text' => $data['text']
                ]
            ];

        } catch (InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}

