<?php

require_once __DIR__ . '/tests/run_tests.php';

$results = runAllTests();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 5 - Тестирование</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🧪 Лабораторная работа 5</h1>
            <p class="subtitle">Тестирование репозиториев и моделей</p>
        </header>

        <!-- Статистика -->
        <div class="stats-grid">
            <div class="stat-card stat-total">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $results['total']; ?></div>
                    <div class="stat-label">Всего тестов</div>
                </div>
            </div>
            <div class="stat-card stat-passed">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $results['passed']; ?></div>
                    <div class="stat-label">Пройдено</div>
                </div>
            </div>
            <div class="stat-card stat-failed">
                <div class="stat-icon">❌</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $results['failed']; ?></div>
                    <div class="stat-label">Провалено</div>
                </div>
            </div>
            <div class="stat-card stat-coverage">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($results['coverage']['percentage'], 1); ?>%</div>
                    <div class="stat-label">Покрытие кода</div>
                </div>
            </div>
        </div>

        <!-- Покрытие кода по классам -->
        <div class="section">
            <h2>📊 Покрытие кода по классам</h2>
            <div class="coverage-grid">
                <?php foreach ($results['coverage']['classes'] as $className => $coverage): ?>
                    <div class="coverage-card">
                        <div class="coverage-header">
                            <span class="coverage-class"><?php echo htmlspecialchars($className); ?></span>
                            <span class="coverage-percent <?php echo $coverage >= 100 ? 'coverage-full' : ($coverage >= 80 ? 'coverage-good' : 'coverage-low'); ?>">
                                <?php echo number_format($coverage, 1); ?>%
                            </span>
                        </div>
                        <div class="coverage-bar">
                            <div class="coverage-fill" style="width: <?php echo $coverage; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Результаты тестов -->
        <div class="section">
            <h2>📋 Результаты тестов</h2>
            <div class="tests-container">
                <?php 
                $groupedTests = [];
                foreach ($results['results'] as $result) {
                    $group = explode(':', $result['name'])[0];
                    if (!isset($groupedTests[$group])) {
                        $groupedTests[$group] = [];
                    }
                    $groupedTests[$group][] = $result;
                }
                ?>

                <?php foreach ($groupedTests as $groupName => $groupTests): ?>
                    <div class="test-group">
                        <h3 class="test-group-title"><?php echo htmlspecialchars($groupName); ?></h3>
                        <div class="test-list">
                            <?php foreach ($groupTests as $result): ?>
                                <div class="test-item test-<?php echo $result['status']; ?>">
                                    <div class="test-status">
                                        <?php if ($result['status'] === 'passed'): ?>
                                            <span class="test-icon">✅</span>
                                        <?php else: ?>
                                            <span class="test-icon">❌</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="test-info">
                                        <div class="test-name"><?php echo htmlspecialchars($result['name']); ?></div>
                                        <?php if ($result['status'] === 'failed' && $result['error']): ?>
                                            <div class="test-error">
                                                <strong>Ошибка:</strong> <?php echo htmlspecialchars($result['error']['message']); ?>
                                                <?php if (isset($result['error']['file'])): ?>
                                                    <br><small>Файл: <?php echo htmlspecialchars(basename($result['error']['file'])); ?>:<?php echo $result['error']['line']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="test-duration"><?php echo $result['duration']; ?>ms</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Детальная информация о тестах -->
        <div class="section">
            <h2>📝 Детальная информация</h2>
            <div class="details-grid">
                <div class="detail-card">
                    <h3>Тесты для PostsRepository</h3>
                    <ul class="test-requirements">
                        <li>✅ Статья сохраняется в репозиторий</li>
                        <li>✅ Репозиторий находит статью по UUID</li>
                        <li>✅ Репозиторий бросает исключение, если статья не найдена</li>
                    </ul>
                </div>
                <div class="detail-card">
                    <h3>Тесты для CommentsRepository</h3>
                    <ul class="test-requirements">
                        <li>✅ Комментарий сохраняется в репозиторий</li>
                        <li>✅ Репозиторий находит комментарий по UUID</li>
                        <li>✅ Репозиторий бросает исключение, если комментарий не найден</li>
                    </ul>
                </div>
                <div class="detail-card">
                    <h3>Покрытие кода</h3>
                    <ul class="test-requirements">
                        <li>✅ Arguments - все методы протестированы</li>
                        <li>✅ UUID - все методы протестированы</li>
                        <li>✅ User - все методы протестированы</li>
                        <li>✅ Post - все методы протестированы</li>
                        <li>✅ Comment - все методы протестированы</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="refresh-section">
            <button onclick="location.reload()" class="btn-refresh">🔄 Запустить тесты заново</button>
        </div>
    </div>
</body>
</html>

