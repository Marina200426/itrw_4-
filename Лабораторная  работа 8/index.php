<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 8 - Логирование</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📋 Лабораторная работа 8</h1>
            <p class="subtitle">Логирование для SQLite-репозиториев</p>
        </header>

        <div class="tabs">
            <button class="tab-button active" onclick="showTab('logs')">📋 Логи</button>
            <button class="tab-button" onclick="showTab('data')">📊 Данные</button>
            <button class="tab-button" onclick="showTab('test')">🧪 Тестирование</button>
        </div>

        <!-- Вкладка логов -->
        <div id="logs-tab" class="tab-content active">
            <div class="section">
                <h2>📋 Логи репозиториев</h2>
                <div class="logs-controls">
                    <button onclick="loadLogs()" class="btn btn-primary">🔄 Обновить логи</button>
                    <button onclick="clearLogsDisplay()" class="btn btn-secondary">🗑️ Очистить отображение</button>
                    <select id="logLevelFilter" onchange="filterLogs()" class="log-filter">
                        <option value="ALL">Все уровни</option>
                        <option value="INFO">INFO</option>
                        <option value="WARNING">WARNING</option>
                        <option value="ERROR">ERROR</option>
                    </select>
                </div>
                <div id="logsContainer" class="logs-container">
                    <div class="loading">Загрузка логов...</div>
                </div>
            </div>
        </div>

        <!-- Вкладка данных -->
        <div id="data-tab" class="tab-content">
            <div class="section">
                <h2>📊 Данные в БД</h2>
                <button onclick="loadData()" class="btn btn-primary" style="margin-bottom: 15px;">🔄 Обновить</button>
                <div id="dataContainer" class="data-container">
                    <div class="loading">Загрузка данных...</div>
                </div>
            </div>
        </div>

        <!-- Вкладка тестирования -->
        <div id="test-tab" class="tab-content">
            <div class="section">
                <h2>🧪 Тестирование логирования</h2>
                <p class="info-text">Выполните действия ниже, чтобы сгенерировать логи. Затем перейдите на вкладку "Логи" для просмотра.</p>
                
                <div class="test-actions">
                    <div class="test-card">
                        <h3>Создать пользователя</h3>
                        <button onclick="testCreateUser()" class="btn btn-test">👤 Создать тестового пользователя</button>
                    </div>
                    
                    <div class="test-card">
                        <h3>Создать статью</h3>
                        <button onclick="testCreatePost()" class="btn btn-test">📝 Создать тестовую статью</button>
                    </div>
                    
                    <div class="test-card">
                        <h3>Создать комментарий</h3>
                        <button onclick="testCreateComment()" class="btn btn-test">💬 Создать тестовый комментарий</button>
                    </div>
                    
                    <div class="test-card">
                        <h3>Попытка получить несуществующий объект</h3>
                        <button onclick="testGetNotFound()" class="btn btn-test">⚠️ Получить несуществующий объект (WARNING)</button>
                    </div>
                </div>
                
                <div id="testResult" class="test-result"></div>
            </div>
        </div>
    </div>

    <script>
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
            
            if (tabName === 'logs') {
                loadLogs();
            } else if (tabName === 'data') {
                loadData();
            }
        }

        async function loadLogs() {
            const container = document.getElementById('logsContainer');
            container.innerHTML = '<div class="loading">Загрузка логов...</div>';
            
            try {
                let apiUrl = '/api/get_logs.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/get_logs.php';
                    response = await fetch(apiUrl);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    container.innerHTML = `<div class="api-response error">
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                let data;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    const text = await response.text();
                    container.innerHTML = `<div class="api-response error">
                        <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                if (data.success) {
                    displayLogs(data.logs || []);
                } else {
                    container.innerHTML = `<div class="api-response error">Ошибка: ${data.error || 'Unknown error'}</div>`;
                }
            } catch (error) {
                container.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        function displayLogs(logs) {
            const container = document.getElementById('logsContainer');
            const filter = document.getElementById('logLevelFilter').value;
            
            let filteredLogs = logs;
            if (filter !== 'ALL') {
                filteredLogs = logs.filter(log => log.level === filter);
            }
            
            if (filteredLogs.length === 0) {
                container.innerHTML = '<div class="empty-logs">Логи не найдены</div>';
                return;
            }
            
            let html = '<div class="logs-list">';
            filteredLogs.forEach(log => {
                const levelClass = log.level.toLowerCase();
                html += `
                    <div class="log-entry log-${levelClass}">
                        <div class="log-header">
                            <span class="log-level">[${log.level}]</span>
                            <span class="log-timestamp">${escapeHtml(log.timestamp)}</span>
                        </div>
                        <div class="log-message">${escapeHtml(log.message)}</div>
                    </div>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
        }

        function filterLogs() {
            loadLogs();
        }

        function clearLogsDisplay() {
            document.getElementById('logsContainer').innerHTML = '<div class="empty-logs">Отображение очищено</div>';
        }

        async function loadData() {
            const container = document.getElementById('dataContainer');
            container.innerHTML = '<div class="loading">Загрузка данных...</div>';
            
            try {
                let apiUrl = '/api/get_data.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/get_data.php';
                    response = await fetch(apiUrl);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    container.innerHTML = `<div class="api-response error">
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                let data;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    const text = await response.text();
                    container.innerHTML = `<div class="api-response error">
                        <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                if (data.success) {
                    let html = '<div class="data-grid">';
                    html += `<div class="data-section"><h3>👥 Пользователи (${data.users.length})</h3>`;
                    html += '<div class="data-list">';
                    data.users.forEach(user => {
                        html += `<div class="data-item">${escapeHtml(user.username)} (${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)})<br><small>UUID: ${escapeHtml(user.uuid)}</small></div>`;
                    });
                    html += '</div></div>';
                    
                    html += `<div class="data-section"><h3>📝 Статьи (${data.posts.length})</h3>`;
                    html += '<div class="data-list">';
                    data.posts.forEach(post => {
                        html += `<div class="data-item">${escapeHtml(post.title)}<br><small>UUID: ${escapeHtml(post.uuid)}</small></div>`;
                    });
                    html += '</div></div>';
                    
                    html += `<div class="data-section"><h3>💬 Комментарии (${data.comments.length})</h3>`;
                    html += '<div class="data-list">';
                    data.comments.forEach(comment => {
                        html += `<div class="data-item">${escapeHtml(comment.text.substring(0, 50))}...<br><small>UUID: ${escapeHtml(comment.uuid)}</small></div>`;
                    });
                    html += '</div></div>';
                    
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `<div class="api-response error">Ошибка: ${data.error}</div>`;
                }
            } catch (error) {
                container.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        async function testCreateUser() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<div class="loading">Создание пользователя...</div>';
            
            try {
                let apiUrl = '/api/test/create_user.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/test/create_user.php';
                    response = await fetch(apiUrl);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                const data = await response.json();
                
                resultDiv.innerHTML = `<div class="api-response ${data.success ? 'success' : 'error'}">
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </div>`;
                
                if (data.success) {
                    setTimeout(() => loadLogs(), 500);
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        async function testCreatePost() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<div class="loading">Создание статьи...</div>';
            
            try {
                let apiUrl = '/api/test/create_post.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/test/create_post.php';
                    response = await fetch(apiUrl);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                const data = await response.json();
                
                resultDiv.innerHTML = `<div class="api-response ${data.success ? 'success' : 'error'}">
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </div>`;
                
                if (data.success) {
                    setTimeout(() => loadLogs(), 500);
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        async function testCreateComment() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<div class="loading">Создание комментария...</div>';
            
            try {
                let apiUrl = '/api/test/create_comment.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/test/create_comment.php';
                    response = await fetch(apiUrl);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                const data = await response.json();
                
                resultDiv.innerHTML = `<div class="api-response ${data.success ? 'success' : 'error'}">
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </div>`;
                
                if (data.success) {
                    setTimeout(() => loadLogs(), 500);
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        async function testGetNotFound() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<div class="loading">Попытка получить несуществующий объект...</div>';
            
            try {
                let apiUrl = '/api/test/get_not_found.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/test/get_not_found.php';
                    response = await fetch(apiUrl);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    resultDiv.innerHTML = `<div class="api-response error">
                        <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${escapeHtml(text.substring(0, 500))}</pre>
                    </div>`;
                    return;
                }
                
                const data = await response.json();
                
                resultDiv.innerHTML = `<div class="api-response ${data.success ? 'success' : 'error'}">
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </div>`;
                
                setTimeout(() => loadLogs(), 500);
            } catch (error) {
                resultDiv.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        // Автообновление логов каждые 5 секунд
        setInterval(() => {
            if (document.getElementById('logs-tab').classList.contains('active')) {
                loadLogs();
            }
        }, 5000);

        window.addEventListener('DOMContentLoaded', function() {
            loadLogs();
            // Проверяем наличие данных, если их нет - предлагаем создать
            setTimeout(() => {
                fetch('/api/get_data.php').then(r => {
                    if (r.ok) {
                        return r.json();
                    }
                    return fetch('api/get_data.php').then(r => r.json());
                }).then(data => {
                    if (data && data.success) {
                        const totalItems = (data.users?.length || 0) + (data.posts?.length || 0) + (data.comments?.length || 0);
                        if (totalItems === 0) {
                            console.log('База данных пуста. Запустите: php init_test_data.php');
                        }
                    }
                }).catch(() => {});
            }, 1000);
        });
    </script>
</body>
</html>

