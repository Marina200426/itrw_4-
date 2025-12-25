<?php

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/RepositoriesTest.php';

$runner = new TestRunner();
$test = new RepositoriesTest();

// Добавляем все тесты
$runner->addTest('UsersRepository save logs INFO', function() use ($test) {
    $test->testUsersRepositorySaveLogsInfo();
});

$runner->addTest('UsersRepository get not found logs WARNING', function() use ($test) {
    $test->testUsersRepositoryGetNotFoundLogsWarning();
});

$runner->addTest('PostsRepository save logs INFO', function() use ($test) {
    $test->testPostsRepositorySaveLogsInfo();
});

$runner->addTest('PostsRepository get not found logs WARNING', function() use ($test) {
    $test->testPostsRepositoryGetNotFoundLogsWarning();
});

$runner->addTest('CommentsRepository save logs INFO', function() use ($test) {
    $test->testCommentsRepositorySaveLogsInfo();
});

$runner->addTest('CommentsRepository get not found logs WARNING', function() use ($test) {
    $test->testCommentsRepositoryGetNotFoundLogsWarning();
});

$runner->addTest('LikesRepository save logs INFO', function() use ($test) {
    $test->testLikesRepositorySaveLogsInfo();
});

$runner->addTest('CommentLikesRepository save logs INFO', function() use ($test) {
    $test->testCommentLikesRepositorySaveLogsInfo();
});

// Запускаем тесты
$results = $runner->run();

// Выводим результаты
echo "=== Результаты тестирования ===\n\n";
echo "Всего тестов: {$results['total']}\n";
echo "Пройдено: {$results['passed']}\n";
echo "Провалено: {$results['failed']}\n\n";

foreach ($results['results'] as $result) {
    $status = $result['status'] === 'passed' ? '✓' : '✗';
    $color = $result['status'] === 'passed' ? "\033[32m" : "\033[31m";
    $reset = "\033[0m";
    
    echo "{$color}{$status}{$reset} {$result['name']} ({$result['duration']}ms)\n";
    
    if ($result['status'] === 'failed' && isset($result['error'])) {
        echo "  Ошибка: {$result['error']['message']}\n";
        echo "  Файл: {$result['error']['file']}:{$result['error']['line']}\n";
    }
}

echo "\n";

if ($results['failed'] === 0) {
    echo "\033[32mВсе тесты пройдены успешно!\033[0m\n";
    exit(0);
} else {
    echo "\033[31mНекоторые тесты провалились.\033[0m\n";
    exit(1);
}

