<?php

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/PostsRepositoryTest.php';
require_once __DIR__ . '/CommentsRepositoryTest.php';
require_once __DIR__ . '/ModelsTest.php';

function runAllTests(): array
{
    $runner = new TestRunner();

    // Тесты для PostsRepository
    $postsTest = new PostsRepositoryTest();
    $runner->addTest('PostsRepository: save post', function() use ($postsTest) {
        $postsTest->testSavePost();
    });
    $runner->addTest('PostsRepository: get post by UUID', function() use ($postsTest) {
        $postsTest->testGetPostByUuid();
    });
    $runner->addTest('PostsRepository: throw exception when post not found', function() use ($postsTest) {
        $postsTest->testGetPostThrowsExceptionWhenNotFound();
    });

    // Тесты для CommentsRepository
    $commentsTest = new CommentsRepositoryTest();
    $runner->addTest('CommentsRepository: save comment', function() use ($commentsTest) {
        $commentsTest->testSaveComment();
    });
    $runner->addTest('CommentsRepository: get comment by UUID', function() use ($commentsTest) {
        $commentsTest->testGetCommentByUuid();
    });
    $runner->addTest('CommentsRepository: throw exception when comment not found', function() use ($commentsTest) {
        $commentsTest->testGetCommentThrowsExceptionWhenNotFound();
    });

    // Тесты для моделей и утилит
    $modelsTest = new ModelsTest();
    $runner->addTest('UUID: generation', function() use ($modelsTest) {
        $modelsTest->testUUIDGeneration();
    });
    $runner->addTest('UUID: validation', function() use ($modelsTest) {
        $modelsTest->testUUIDValidation();
    });
    $runner->addTest('UUID: equals', function() use ($modelsTest) {
        $modelsTest->testUUIDEquals();
    });
    $runner->addTest('Arguments: not empty', function() use ($modelsTest) {
        $modelsTest->testArgumentsNotEmpty();
    });
    $runner->addTest('Arguments: not null', function() use ($modelsTest) {
        $modelsTest->testArgumentsNotNull();
    });
    $runner->addTest('Arguments: string not empty', function() use ($modelsTest) {
        $modelsTest->testArgumentsStringNotEmpty();
    });
    $runner->addTest('User: creation', function() use ($modelsTest) {
        $modelsTest->testUserCreation();
    });
    $runner->addTest('User: validation', function() use ($modelsTest) {
        $modelsTest->testUserValidation();
    });
    $runner->addTest('Post: creation', function() use ($modelsTest) {
        $modelsTest->testPostCreation();
    });
    $runner->addTest('Post: validation', function() use ($modelsTest) {
        $modelsTest->testPostValidation();
    });
    $runner->addTest('Comment: creation', function() use ($modelsTest) {
        $modelsTest->testCommentCreation();
    });
    $runner->addTest('Comment: validation', function() use ($modelsTest) {
        $modelsTest->testCommentValidation();
    });

    return $runner->run();
}

// Если запускается из командной строки
if (php_sapi_name() === 'cli') {
    $results = runAllTests();
    
    echo "\n=== Результаты тестирования ===\n\n";
    echo "Всего тестов: {$results['total']}\n";
    echo "Пройдено: {$results['passed']}\n";
    echo "Провалено: {$results['failed']}\n\n";
    
    foreach ($results['results'] as $result) {
        $status = $result['status'] === 'passed' ? '✓' : '✗';
        echo "{$status} {$result['name']} ({$result['duration']}ms)\n";
        if ($result['status'] === 'failed' && $result['error']) {
            echo "  Ошибка: {$result['error']['message']}\n";
        }
    }
    
    echo "\n=== Покрытие кода ===\n";
    echo "Общее покрытие: {$results['coverage']['percentage']}%\n";
    echo "Arguments: {$results['coverage']['classes']['Arguments']}%\n";
    echo "UUID: {$results['coverage']['classes']['UUID']}%\n";
    echo "User: {$results['coverage']['classes']['User']}%\n";
    echo "Post: {$results['coverage']['classes']['Post']}%\n";
    echo "Comment: {$results['coverage']['classes']['Comment']}%\n";
}

