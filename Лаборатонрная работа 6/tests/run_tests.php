<?php

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/CreatePostTest.php';

function runAllTests(): array
{
    $runner = new TestRunner();

    $createPostTest = new CreatePostTest();
    $runner->addTest('CreatePost: returns success', function() use ($createPostTest) {
        $createPostTest->testCreatePostReturnsSuccess();
    });
    $runner->addTest('CreatePost: returns error for invalid UUID', function() use ($createPostTest) {
        $createPostTest->testCreatePostReturnsErrorForInvalidUUID();
    });
    $runner->addTest('CreatePost: returns error for non-existent user', function() use ($createPostTest) {
        $createPostTest->testCreatePostReturnsErrorForNonExistentUser();
    });
    $runner->addTest('CreatePost: returns error for missing fields', function() use ($createPostTest) {
        $createPostTest->testCreatePostReturnsErrorForMissingFields();
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
}

