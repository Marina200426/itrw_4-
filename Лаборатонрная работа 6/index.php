<?php

require_once __DIR__ . '/tests/run_tests.php';

$results = runAllTests();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 6 - REST API и Тестирование</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🚀 Лабораторная работа 6</h1>
            <p class="subtitle">REST API для работы со статьями и комментариями</p>
        </header>

        <!-- Статистика тестов -->
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
        </div>

        <!-- Результаты тестов -->
        <div class="section">
            <h2>📋 Результаты тестов CreatePost</h2>
            <div class="tests-container">
                <?php foreach ($results['results'] as $result): ?>
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

        <!-- API Endpoints -->
        <div class="section">
            <h2>🔌 API Endpoints</h2>
            <div class="endpoints-grid">
                <div class="endpoint-card">
                    <h3>POST /api/posts/comment</h3>
                    <p>Создание комментария к статье</p>
                    <div class="endpoint-example">
                        <strong>Request:</strong>
                        <pre>POST http://127.0.0.1:8000/api/posts/comment
Content-Type: application/json

{
  "author_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "post_uuid": "660e8400-e29b-41d4-a716-446655440001",
  "text": "Отличная статья!"
}</pre>
                    </div>
                    <div class="endpoint-example">
                        <strong>Response (201):</strong>
                        <pre>{
  "success": true,
  "message": "Comment created successfully",
  "data": {
    "uuid": "...",
    "post_uuid": "...",
    "author_uuid": "...",
    "text": "..."
  }
}</pre>
                    </div>
                </div>

                <div class="endpoint-card">
                    <h3>DELETE /api/posts?uuid=&lt;UUID&gt;</h3>
                    <p>Удаление статьи</p>
                    <div class="endpoint-example">
                        <strong>Request:</strong>
                        <pre>DELETE http://127.0.0.1:8000/api/posts?uuid=660e8400-e29b-41d4-a716-446655440001</pre>
                    </div>
                    <div class="endpoint-example">
                        <strong>Response (200):</strong>
                        <pre>{
  "success": true,
  "message": "Post deleted successfully"
}</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Существующие данные -->
        <div class="section">
            <h2>📊 Существующие данные в БД</h2>
            <button onclick="loadExistingData()" class="btn btn-primary" style="margin-bottom: 15px;">🔄 Обновить список</button>
            <div id="existingData" class="existing-data-container">
                <div class="loading">Загрузка данных...</div>
            </div>
        </div>

        <!-- Тестирование API -->
        <div class="section">
            <h2>🧪 Тестирование API</h2>
            <div class="api-test-container">
                <div class="api-test-card">
                    <h3>Создать комментарий</h3>
                    <form id="createCommentForm" class="api-form">
                        <div class="form-group">
                            <label>Author UUID:</label>
                            <input type="text" name="author_uuid" required placeholder="550e8400-e29b-41d4-a716-446655440000">
                        </div>
                        <div class="form-group">
                            <label>Post UUID:</label>
                            <input type="text" name="post_uuid" required placeholder="660e8400-e29b-41d4-a716-446655440001">
                        </div>
                        <div class="form-group">
                            <label>Text:</label>
                            <textarea name="text" required rows="3" placeholder="Текст комментария..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Создать комментарий</button>
                    </form>
                    <div id="createCommentResult" class="api-result"></div>
                </div>

                <div class="api-test-card">
                    <h3>Удалить статью</h3>
                    <form id="deletePostForm" class="api-form">
                        <div class="form-group">
                            <label>Post UUID:</label>
                            <input type="text" name="uuid" required placeholder="660e8400-e29b-41d4-a716-446655440001">
                        </div>
                        <button type="submit" class="btn btn-danger">Удалить статью</button>
                    </form>
                    <div id="deletePostResult" class="api-result"></div>
                </div>
            </div>
        </div>

        <div class="refresh-section">
            <button onclick="location.reload()" class="btn-refresh">🔄 Запустить тесты заново</button>
        </div>
    </div>

    <script>
        // Загрузка существующих данных
        async function loadExistingData() {
            const container = document.getElementById('existingData');
            container.innerHTML = '<div class="loading">Загрузка данных...</div>';
            
            try {
                // Пробуем разные пути для совместимости с разными серверами
                let apiUrl = '/api/get_data.php';
                let response = await fetch(apiUrl);
                
                // Если 404, пробуем альтернативный путь
                if (response.status === 404) {
                    apiUrl = 'api/get_data.php';
                    response = await fetch(apiUrl);
                }
                
                // Если все еще 404, используем прямой путь через index.php API
                if (response.status === 404) {
                    apiUrl = '/api/index.php?action=get_data';
                    response = await fetch(apiUrl);
                }
                
                // Проверяем Content-Type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    container.innerHTML = `<div class="api-response error">
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${text.substring(0, 500)}</pre>
                    </div>`;
                    return;
                }
                
                const data = await response.json();
                
                if (data.success) {
                    let html = '<div class="data-grid">';
                    
                    // Пользователи
                    html += '<div class="data-section"><h3>👥 Пользователи (' + data.users.length + ')</h3>';
                    if (data.users.length > 0) {
                        html += '<div class="data-list">';
                        data.users.forEach(user => {
                            html += `<div class="data-item">
                                <strong>${user.username}</strong> (${user.first_name} ${user.last_name})<br>
                                <small class="uuid-text">UUID: ${user.uuid}</small>
                                <button onclick="copyToClipboard('${user.uuid}')" class="btn-copy">📋</button>
                            </div>`;
                        });
                        html += '</div>';
                    } else {
                        html += '<p class="empty-data">Нет пользователей</p>';
                    }
                    html += '</div>';
                    
                    // Статьи
                    html += '<div class="data-section"><h3>📰 Статьи (' + data.posts.length + ')</h3>';
                    if (data.posts.length > 0) {
                        html += '<div class="data-list">';
                        data.posts.forEach(post => {
                            html += `<div class="data-item">
                                <strong>${post.title}</strong><br>
                                <small class="uuid-text">UUID: ${post.uuid}</small>
                                <button onclick="copyToClipboard('${post.uuid}')" class="btn-copy">📋</button>
                                <button onclick="fillDeleteForm('${post.uuid}')" class="btn-use">Использовать для удаления</button>
                            </div>`;
                        });
                        html += '</div>';
                    } else {
                        html += '<p class="empty-data">Нет статей</p>';
                    }
                    html += '</div>';
                    
                    // Комментарии
                    html += '<div class="data-section"><h3>💬 Комментарии (' + (data.comments ? data.comments.length : 0) + ')</h3>';
                    if (data.comments && data.comments.length > 0) {
                        html += '<div class="data-list">';
                        data.comments.forEach(comment => {
                            const authorName = comment.author_username ? 
                                `${comment.author_first_name} ${comment.author_last_name} (@${comment.author_username})` : 
                                'Неизвестный автор';
                            const postTitle = comment.post_title || 'Статья удалена';
                            html += `<div class="data-item comment-item">
                                <div class="comment-header-info">
                                    <strong>${escapeHtml(comment.text)}</strong><br>
                                    <small>👤 Автор: ${authorName}</small><br>
                                    <small>📝 К статье: ${escapeHtml(postTitle)}</small>
                                </div>
                                <small class="uuid-text">UUID: ${comment.uuid}</small>
                                <button onclick="copyToClipboard('${comment.uuid}')" class="btn-copy">📋</button>
                                <button onclick="fillCommentForm('${comment.posts_uuid}', '${comment.author_uuid}')" class="btn-use-small">Использовать для создания</button>
                                <button onclick="deleteCommentFromList('${comment.uuid}')" class="btn-delete-comment-list">🗑️ Удалить</button>
                            </div>`;
                        });
                        html += '</div>';
                    } else {
                        html += '<p class="empty-data">Нет комментариев</p>';
                    }
                    html += '</div>';
                    
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="api-response error">Ошибка загрузки данных</div>';
                }
            } catch (error) {
                container.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }
        
        // Копирование UUID в буфер обмена
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('UUID скопирован: ' + text);
            });
        }
        
        // Заполнение формы удаления
        function fillDeleteForm(uuid) {
            document.querySelector('#deletePostForm input[name="uuid"]').value = uuid;
            document.getElementById('deletePostForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Заполнение формы создания комментария
        function fillCommentForm(postUuid, authorUuid) {
            document.querySelector('#createCommentForm input[name="post_uuid"]').value = postUuid;
            document.querySelector('#createCommentForm input[name="author_uuid"]').value = authorUuid;
            document.getElementById('createCommentForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Экранирование HTML для безопасности
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Принудительное удаление статьи со всеми комментариями
        async function deletePostWithForce(postUuid) {
            if (!confirm('⚠️ ВНИМАНИЕ!\n\nВы собираетесь удалить статью и ВСЕ комментарии к ней.\n\nЭто действие нельзя отменить!\n\nПродолжить?')) {
                return;
            }
            
            const resultDiv = document.getElementById('deletePostResult');
            resultDiv.innerHTML = '<div class="loading">Удаление статьи и комментариев...</div>';
            
            try {
                let apiUrl = `/api/posts/delete.php?uuid=${encodeURIComponent(postUuid)}&force=true`;
                let response;
                
                try {
                    response = await fetch(apiUrl, { method: 'DELETE' });
                } catch (fetchError) {
                    apiUrl = `api/posts/delete.php?uuid=${encodeURIComponent(postUuid)}&force=true`;
                    response = await fetch(apiUrl, { method: 'DELETE' });
                }
                
                // Если 404, пробуем через основной API endpoint
                if (response.status === 404 || !response.ok) {
                    apiUrl = `/api/index.php?uuid=${encodeURIComponent(postUuid)}&force=true&_method=DELETE`;
                    response = await fetch(apiUrl, { method: 'DELETE' });
                }
                
                let result;
                const contentType = response.headers.get('content-type');
                
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        <strong>URL:</strong> ${apiUrl}<br>
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Content-Type:</strong> ${contentType || 'не указан'}<br>
                        <details>
                            <summary>Ответ сервера</summary>
                            <pre>${text.substring(0, 1000)}</pre>
                        </details>
                    </div>`;
                    return;
                }
                
                try {
                    result = await response.json();
                } catch (jsonError) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                        <pre>${text.substring(0, 500)}</pre>
                    </div>`;
                    return;
                }
                
                resultDiv.innerHTML = `<div class="api-response ${response.ok ? 'success' : 'error'}">
                    <strong>Status:</strong> ${response.status}<br>
                    ${result.warning ? `<p class="warning-message">⚠️ ${result.warning}</p>` : ''}
                    <pre>${JSON.stringify(result, null, 2)}</pre>
                </div>`;
                
                if (response.ok) {
                    setTimeout(() => loadExistingData(), 1000);
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка сети: ${error.message}</div>`;
            }
        }
        
        // Загружаем данные при загрузке страницы
        loadExistingData();
        
        // Обработка создания комментария
        document.getElementById('createCommentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {
                author_uuid: formData.get('author_uuid'),
                post_uuid: formData.get('post_uuid'),
                text: formData.get('text')
            };

            const resultDiv = document.getElementById('createCommentResult');
            resultDiv.innerHTML = '<div class="loading">Отправка запроса...</div>';

            try {
                // Пробуем разные пути для совместимости
                let apiUrl = '/api/posts/comment.php';
                let response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                // Если 404, пробуем альтернативный путь
                if (response.status === 404) {
                    apiUrl = '/api/posts/comment';
                    response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                }

                // Если все еще 404, пробуем без начального слэша
                if (response.status === 404) {
                    apiUrl = 'api/posts/comment.php';
                    response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                }

                let result;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        <strong>URL:</strong> ${apiUrl}<br>
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <pre>${text.substring(0, 500)}</pre>
                    </div>`;
                    return;
                }

                resultDiv.innerHTML = `<div class="api-response ${response.ok ? 'success' : 'error'}">
                    <strong>Status:</strong> ${response.status}<br>
                    <pre>${JSON.stringify(result, null, 2)}</pre>
                </div>`;
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка сети: ${error.message}</div>`;
            }
        });

        // Обработка удаления статьи
        document.getElementById('deletePostForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const uuid = formData.get('uuid');

            const resultDiv = document.getElementById('deletePostResult');
            resultDiv.innerHTML = '<div class="loading">Отправка запроса...</div>';

            try {
                // Пробуем разные пути для совместимости
                let apiUrl = `/api/posts/delete.php?uuid=${encodeURIComponent(uuid)}`;
                let response;
                
                try {
                    response = await fetch(apiUrl, {
                        method: 'DELETE'
                    });
                } catch (fetchError) {
                    // Если ошибка сети, пробуем альтернативный путь
                    apiUrl = `api/posts/delete.php?uuid=${encodeURIComponent(uuid)}`;
                    response = await fetch(apiUrl, {
                        method: 'DELETE'
                    });
                }

                // Если 404 или ошибка, пробуем через основной API endpoint
                if (response.status === 404 || !response.ok) {
                    // Пробуем DELETE
                    apiUrl = `/api/index.php?uuid=${encodeURIComponent(uuid)}&_method=DELETE`;
                    response = await fetch(apiUrl, {
                        method: 'DELETE'
                    });
                    
                    // Если все еще ошибка, пробуем GET с _method=DELETE (для OpenServer)
                    if (response.status === 404 || !response.ok) {
                        apiUrl = `/api/index.php?uuid=${encodeURIComponent(uuid)}&_method=DELETE`;
                        response = await fetch(apiUrl, {
                            method: 'GET'
                        });
                    }
                }

                // Если все еще 404, пробуем без начального слэша
                if (response.status === 404) {
                    apiUrl = `api/posts/delete.php?uuid=${encodeURIComponent(uuid)}`;
                    response = await fetch(apiUrl, {
                        method: 'DELETE'
                    });
                }

                let result;
                const contentType = response.headers.get('content-type');
                
                // Если это не JSON, пробуем получить текст и показать ошибку
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        <strong>URL:</strong> ${apiUrl}<br>
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ (вероятно, файл не найден)<br>
                        <strong>Content-Type:</strong> ${contentType || 'не указан'}<br>
                        <details>
                            <summary>Ответ сервера</summary>
                            <pre>${text.substring(0, 1000)}</pre>
                        </details>
                        <p><strong>Попробуйте:</strong> Проверьте, что файл <code>api/posts/delete.php</code> существует</p>
                    </div>`;
                    return;
                }
                
                try {
                    result = await response.json();
                } catch (jsonError) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                        <pre>${text.substring(0, 500)}</pre>
                    </div>`;
                    return;
                }

                // Специальная обработка для ошибки с комментариями
                if (response.status === 409 && result.error && result.comments_count > 0) {
                    let commentsHtml = '<div class="comments-warning">';
                    commentsHtml += `<p><strong>⚠️ Внимание!</strong> ${result.message}</p>`;
                    commentsHtml += `<p>Найдено комментариев: <strong>${result.comments_count}</strong></p>`;
                    commentsHtml += '<div class="comments-list-warning">';
                    result.comments.forEach((comment, index) => {
                        commentsHtml += `<div class="comment-item-warning">
                            <span>${index + 1}. ${escapeHtml(comment.text.substring(0, 50))}${comment.text.length > 50 ? '...' : ''}</span>
                            <button onclick="deleteComment('${comment.uuid}')" class="btn-delete-comment">Удалить</button>
                        </div>`;
                    });
                    commentsHtml += '</div>';
                    commentsHtml += `<p><button onclick="deletePostWithForce('${uuid}')" class="btn-force-delete">🗑️ Удалить статью и все комментарии</button></p>`;
                    commentsHtml += '</div>';
                    
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        ${commentsHtml}
                        <details style="margin-top: 10px;">
                            <summary>Детали ответа</summary>
                            <pre>${JSON.stringify(result, null, 2)}</pre>
                        </details>
                    </div>`;
                } else {
                    resultDiv.innerHTML = `<div class="api-response ${response.ok ? 'success' : 'error'}">
                        <strong>Status:</strong> ${response.status}<br>
                        ${result.warning ? `<p class="warning-message">⚠️ ${result.warning}</p>` : ''}
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    </div>`;
                }
                
                // Обновляем список данных после успешного удаления
                if (response.ok) {
                    setTimeout(() => loadExistingData(), 1000);
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка сети: ${error.message}</div>`;
            }
        });
        
        // Удаление комментария из списка
        async function deleteCommentFromList(commentUuid) {
            if (!confirm('Вы уверены, что хотите удалить этот комментарий?')) {
                return;
            }
            
            try {
                let apiUrl = `/api/comments/delete.php?uuid=${encodeURIComponent(commentUuid)}`;
                let response = await fetch(apiUrl, { method: 'DELETE' });
                
                if (response.status === 404) {
                    apiUrl = `api/comments/delete.php?uuid=${encodeURIComponent(commentUuid)}`;
                    response = await fetch(apiUrl, { method: 'DELETE' });
                }
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Комментарий успешно удален!');
                    loadExistingData();
                } else {
                    alert('Ошибка: ' + (result.error || result.message || 'Неизвестная ошибка'));
                }
            } catch (error) {
                alert('Ошибка сети: ' + error.message);
            }
        }
        
        // Удаление комментария (из предупреждения при удалении статьи)
        async function deleteComment(commentUuid) {
            if (!confirm('Вы уверены, что хотите удалить этот комментарий?')) {
                return;
            }
            
            try {
                let apiUrl = `/api/comments/delete.php?uuid=${encodeURIComponent(commentUuid)}`;
                let response = await fetch(apiUrl, { method: 'DELETE' });
                
                if (response.status === 404) {
                    apiUrl = `api/comments/delete.php?uuid=${encodeURIComponent(commentUuid)}`;
                    response = await fetch(apiUrl, { method: 'DELETE' });
                }
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Комментарий успешно удален!');
                    loadExistingData();
                    // Обновляем форму удаления статьи, если она открыта
                    const deleteResult = document.getElementById('deletePostResult');
                    if (deleteResult && deleteResult.innerHTML.includes('comments-warning')) {
                        // Перезагружаем данные для обновления списка комментариев
                        setTimeout(() => {
                            const uuidInput = document.querySelector('#deletePostForm input[name="uuid"]');
                            if (uuidInput && uuidInput.value) {
                                document.getElementById('deletePostForm').dispatchEvent(new Event('submit'));
                            }
                        }, 500);
                    }
                } else {
                    alert('Ошибка: ' + (result.error || result.message || 'Неизвестная ошибка'));
                }
            } catch (error) {
                alert('Ошибка сети: ' + error.message);
            }
        }
        
        // Принудительное удаление статьи со всеми комментариями
        async function deletePostWithForce(postUuid) {
            if (!confirm('⚠️ ВНИМАНИЕ!\n\nВы собираетесь удалить статью и ВСЕ комментарии к ней.\n\nЭто действие нельзя отменить!\n\nПродолжить?')) {
                return;
            }
            
            const resultDiv = document.getElementById('deletePostResult');
            resultDiv.innerHTML = '<div class="loading">Удаление статьи и комментариев...</div>';
            
            try {
                let apiUrl = `/api/posts/delete.php?uuid=${encodeURIComponent(postUuid)}&force=true`;
                let response;
                
                try {
                    response = await fetch(apiUrl, { method: 'DELETE' });
                } catch (fetchError) {
                    apiUrl = `api/posts/delete.php?uuid=${encodeURIComponent(postUuid)}&force=true`;
                    response = await fetch(apiUrl, { method: 'DELETE' });
                }
                
                // Если 404, пробуем через основной API endpoint
                if (response.status === 404 || !response.ok) {
                    // Пробуем DELETE
                    apiUrl = `/api/index.php?uuid=${encodeURIComponent(postUuid)}&force=true&_method=DELETE`;
                    response = await fetch(apiUrl, { method: 'DELETE' });
                    
                    // Если все еще ошибка, пробуем GET с _method=DELETE (для OpenServer)
                    if (response.status === 404 || !response.ok) {
                        apiUrl = `/api/index.php?uuid=${encodeURIComponent(postUuid)}&force=true&_method=DELETE`;
                        response = await fetch(apiUrl, { method: 'GET' });
                    }
                }
                
                if (response.status === 404) {
                    apiUrl = `api/posts/delete.php?uuid=${encodeURIComponent(postUuid)}&force=true`;
                    response = await fetch(apiUrl, { method: 'DELETE' });
                }
                
                let result;
                const contentType = response.headers.get('content-type');
                
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        <strong>URL:</strong> ${apiUrl}<br>
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Content-Type:</strong> ${contentType || 'не указан'}<br>
                        <details>
                            <summary>Ответ сервера</summary>
                            <pre>${text.substring(0, 1000)}</pre>
                        </details>
                    </div>`;
                    return;
                }
                
                try {
                    result = await response.json();
                } catch (jsonError) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Status:</strong> ${response.status}<br>
                        <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                        <pre>${text.substring(0, 500)}</pre>
                    </div>`;
                    return;
                }
                
                resultDiv.innerHTML = `<div class="api-response ${response.ok ? 'success' : 'error'}">
                    <strong>Status:</strong> ${response.status}<br>
                    ${result.warning ? `<p class="warning-message">⚠️ ${result.warning}</p>` : ''}
                    <pre>${JSON.stringify(result, null, 2)}</pre>
                </div>`;
                
                if (response.ok) {
                    setTimeout(() => loadExistingData(), 1000);
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка сети: ${error.message}</div>`;
            }
        }
    </script>
</body>
</html>

