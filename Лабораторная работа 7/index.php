<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 7 - Система лайков</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>❤️ Лабораторная работа 7</h1>
            <p class="subtitle">Система лайков для статей и комментариев</p>
        </header>

        <!-- Вкладки -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('data')">📊 Данные</button>
            <button class="tab-button" onclick="showTab('posts')">📝 Статьи</button>
            <button class="tab-button" onclick="showTab('comments')">💬 Комментарии</button>
            <button class="tab-button" onclick="showTab('api')">🧪 API Тест</button>
        </div>

        <!-- Вкладка данных -->
        <div id="data-tab" class="tab-content active">
            <div class="section">
                <h2>📊 Существующие данные в БД</h2>
                <button onclick="loadExistingData()" class="btn btn-primary" style="margin-bottom: 15px;">🔄 Обновить список</button>
                <div id="existingData" class="existing-data-container">
                    <div class="loading">Загрузка данных...</div>
                </div>
            </div>
        </div>

        <!-- Вкладка статей -->
        <div id="posts-tab" class="tab-content">
            <div class="section">
                <h2>📝 Статьи с лайками</h2>
                <button onclick="loadPostsView()" class="btn btn-primary" style="margin-bottom: 15px;">🔄 Обновить</button>
                <div id="postsView" class="posts-view-container">
                    <div class="loading">Загрузка статей...</div>
                </div>
            </div>
        </div>

        <!-- Вкладка комментариев -->
        <div id="comments-tab" class="tab-content">
            <!-- Форма создания комментария -->
            <div class="section">
                <h2>✍️ Создать новый комментарий</h2>
                <form id="createCommentForm" class="create-form">
                    <div class="form-group">
                        <label>Статья (UUID статьи):</label>
                        <select id="commentPostSelect" name="post_uuid" required>
                            <option value="">Загрузка статей...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Автор (UUID пользователя):</label>
                        <select id="commentAuthorSelect" name="author_uuid" required>
                            <option value="">Загрузка пользователей...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Текст комментария:</label>
                        <textarea name="text" required rows="4" placeholder="Введите текст комментария"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">💬 Создать комментарий</button>
                </form>
                <div id="createCommentResult" class="api-result"></div>
            </div>

            <!-- Список комментариев -->
            <div class="section">
                <h2>💬 Комментарии с лайками</h2>
                <button onclick="loadCommentsView()" class="btn btn-primary" style="margin-bottom: 15px;">🔄 Обновить</button>
                <div id="commentsView" class="comments-view-container">
                    <div class="loading">Загрузка комментариев...</div>
                </div>
            </div>
        </div>

        <!-- Вкладка API тестирования -->
        <div id="api-tab" class="tab-content">

            <!-- Тестирование API - Лайки статей -->
            <div class="section">
                <h2>🧪 Тестирование API - Лайки статей</h2>
                <div class="api-test-container">
                    <div class="api-test-card">
                        <h3>Добавить лайк к статье</h3>
                        <form id="likePostForm" class="api-form">
                            <div class="form-group">
                                <label>Post UUID:</label>
                                <input type="text" name="post_uuid" required placeholder="660e8400-e29b-41d4-a716-446655440001">
                            </div>
                            <div class="form-group">
                                <label>User UUID:</label>
                                <input type="text" name="user_uuid" required placeholder="550e8400-e29b-41d4-a716-446655440001">
                            </div>
                            <button type="submit" class="btn btn-like">❤️ Поставить лайк</button>
                        </form>
                        <div id="likePostResult" class="api-result"></div>
                    </div>
                </div>
            </div>

            <!-- Тестирование API - Лайки комментариев -->
            <div class="section">
                <h2>🧪 Тестирование API - Лайки комментариев</h2>
                <div class="api-test-container">
                    <div class="api-test-card">
                        <h3>Добавить лайк к комментарию</h3>
                        <form id="likeCommentForm" class="api-form">
                            <div class="form-group">
                                <label>Comment UUID:</label>
                                <input type="text" name="comment_uuid" required placeholder="770e8400-e29b-41d4-a716-446655440001">
                            </div>
                            <div class="form-group">
                                <label>User UUID:</label>
                                <input type="text" name="user_uuid" required placeholder="550e8400-e29b-41d4-a716-446655440001">
                            </div>
                            <button type="submit" class="btn btn-like">❤️ Поставить лайк</button>
                        </form>
                        <div id="likeCommentResult" class="api-result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Функция для экранирования HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Загрузка существующих данных
        async function loadExistingData() {
            const container = document.getElementById('existingData');
            container.innerHTML = '<div class="loading">Загрузка данных...</div>';
            
            try {
                let apiUrl = '/api/get_data.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/get_data.php';
                    response = await fetch(apiUrl);
                }
                
                if (response.status === 404) {
                    apiUrl = '/api/index.php?action=get_data';
                    response = await fetch(apiUrl);
                }
                
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
                    
                    // Статьи с лайками
                    html += '<div class="data-section"><h3>📰 Статьи (' + data.posts.length + ')</h3>';
                    if (data.posts.length > 0) {
                        html += '<div class="data-list">';
                        data.posts.forEach(post => {
                            const likesCount = post.likes_count || 0;
                            html += `<div class="data-item">
                                <strong>${escapeHtml(post.title)}</strong><br>
                                <div class="likes-count">❤️ ${likesCount} лайков</div>
                                <small class="uuid-text">UUID: ${post.uuid}</small>
                                <button onclick="copyToClipboard('${post.uuid}')" class="btn-copy">📋</button>
                                <button onclick="fillLikePostForm('${post.uuid}')" class="btn-use">Использовать для лайка</button>
                                <button onclick="likePostQuick('${post.uuid}')" class="btn-like-item">❤️ Быстрый лайк</button>
                            </div>`;
                        });
                        html += '</div>';
                    } else {
                        html += '<p class="empty-data">Нет статей</p>';
                    }
                    html += '</div>';
                    
                    // Комментарии с лайками
                    html += '<div class="data-section"><h3>💬 Комментарии (' + (data.comments ? data.comments.length : 0) + ')</h3>';
                    if (data.comments && data.comments.length > 0) {
                        html += '<div class="data-list">';
                        data.comments.forEach(comment => {
                            const authorName = comment.author_username ? 
                                `${comment.author_first_name} ${comment.author_last_name} (@${comment.author_username})` : 
                                'Неизвестный автор';
                            const postTitle = comment.post_title || 'Статья удалена';
                            const likesCount = comment.likes_count || 0;
                            html += `<div class="data-item comment-item">
                                <div class="comment-header-info">
                                    <strong>${escapeHtml(comment.text)}</strong><br>
                                    <small>👤 Автор: ${authorName}</small><br>
                                    <small>📝 К статье: ${escapeHtml(postTitle)}</small>
                                    <div class="likes-count">❤️ ${likesCount} лайков</div>
                                </div>
                                <small class="uuid-text">UUID: ${comment.uuid}</small>
                                <button onclick="copyToClipboard('${comment.uuid}')" class="btn-copy">📋</button>
                                <button onclick="fillLikeCommentForm('${comment.uuid}')" class="btn-use">Использовать для лайка</button>
                                <button onclick="likeCommentQuick('${comment.uuid}')" class="btn-like-item">❤️ Быстрый лайк</button>
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
        
        // Заполнение формы лайка статьи
        function fillLikePostForm(postUuid) {
            document.querySelector('#likePostForm input[name="post_uuid"]').value = postUuid;
        }
        
        // Заполнение формы лайка комментария
        function fillLikeCommentForm(commentUuid) {
            document.querySelector('#likeCommentForm input[name="comment_uuid"]').value = commentUuid;
        }
        
        // Быстрый лайк статьи (использует первого пользователя)
        async function likePostQuick(postUuid) {
            try {
                const response = await fetch('/api/get_data.php');
                const data = await response.json();
                
                if (data.success && data.users.length > 0) {
                    const userUuid = data.users[0].uuid;
                    await likePost(postUuid, userUuid);
                } else {
                    alert('Нет пользователей для лайка. Создайте пользователя сначала.');
                }
            } catch (error) {
                alert('Ошибка: ' + error.message);
            }
        }
        
        // Быстрый лайк комментария (использует первого пользователя)
        async function likeCommentQuick(commentUuid) {
            try {
                const response = await fetch('/api/get_data.php');
                const data = await response.json();
                
                if (data.success && data.users.length > 0) {
                    const userUuid = data.users[0].uuid;
                    await likeComment(commentUuid, userUuid);
                } else {
                    alert('Нет пользователей для лайка. Создайте пользователя сначала.');
                }
            } catch (error) {
                alert('Ошибка: ' + error.message);
            }
        }
        
        // Функция для добавления лайка к статье
        async function likePost(postUuid, userUuid) {
            const resultDiv = document.getElementById('likePostResult');
            if (resultDiv) {
                resultDiv.innerHTML = '<div class="loading">Отправка запроса...</div>';
            }

            try {
                let apiUrl = '/api/posts/like.php';
                let response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        post_uuid: postUuid,
                        user_uuid: userUuid
                    })
                });

                if (response.status === 404) {
                    apiUrl = 'api/posts/like.php';
                    response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            post_uuid: postUuid,
                            user_uuid: userUuid
                        })
                    });
                }

                let result;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    try {
                        result = await response.json();
                    } catch (jsonError) {
                        const text = await response.text();
                        if (resultDiv) {
                            resultDiv.innerHTML = `<div class="api-response error">
                                <strong>Status:</strong> ${response.status}<br>
                                <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                                <strong>Ответ сервера:</strong><br>
                                <pre>${escapeHtml(text.substring(0, 500))}</pre>
                            </div>`;
                        } else {
                            alert(`Ошибка: ${response.status}\n${text.substring(0, 200)}`);
                        }
                        return;
                    }
                } else {
                    const text = await response.text();
                    if (resultDiv) {
                        resultDiv.innerHTML = `<div class="api-response error">
                            <strong>Status:</strong> ${response.status}<br>
                            <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                            <pre>${escapeHtml(text.substring(0, 500))}</pre>
                        </div>`;
                    } else {
                        alert(`Ошибка: ${response.status}\n${text.substring(0, 200)}`);
                    }
                    return;
                }
                
                if (resultDiv) {
                    resultDiv.innerHTML = `<div class="api-response ${response.ok ? 'success' : 'error'}">
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    </div>`;
                }
                
                if (response.ok) {
                    setTimeout(() => {
                        loadExistingData();
                        loadPostsView();
                    }, 1000);
                } else {
                    if (!resultDiv) {
                        alert(`Ошибка: ${result.error || result.message || 'Неизвестная ошибка'}`);
                    }
                }
            } catch (error) {
                if (resultDiv) {
                    resultDiv.innerHTML = `<div class="api-response error">Ошибка сети: ${error.message}</div>`;
                } else {
                    alert(`Ошибка сети: ${error.message}`);
                }
            }
        }
        
        // Функция для добавления лайка к комментарию
        async function likeComment(commentUuid, userUuid) {
            const resultDiv = document.getElementById('likeCommentResult');
            if (resultDiv) {
                resultDiv.innerHTML = '<div class="loading">Отправка запроса...</div>';
            }

            try {
                let apiUrl = '/api/comments/like.php';
                let response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        comment_uuid: commentUuid,
                        user_uuid: userUuid
                    })
                });

                if (response.status === 404) {
                    apiUrl = 'api/comments/like.php';
                    response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            comment_uuid: commentUuid,
                            user_uuid: userUuid
                        })
                    });
                }

                let result;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    try {
                        result = await response.json();
                    } catch (jsonError) {
                        const text = await response.text();
                        if (resultDiv) {
                            resultDiv.innerHTML = `<div class="api-response error">
                                <strong>Status:</strong> ${response.status}<br>
                                <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                                <strong>Ответ сервера:</strong><br>
                                <pre>${escapeHtml(text.substring(0, 500))}</pre>
                            </div>`;
                        } else {
                            alert(`Ошибка: ${response.status}\n${text.substring(0, 200)}`);
                        }
                        return;
                    }
                } else {
                    const text = await response.text();
                    if (resultDiv) {
                        resultDiv.innerHTML = `<div class="api-response error">
                            <strong>Status:</strong> ${response.status}<br>
                            <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                            <pre>${escapeHtml(text.substring(0, 500))}</pre>
                        </div>`;
                    } else {
                        alert(`Ошибка: ${response.status}\n${text.substring(0, 200)}`);
                    }
                    return;
                }
                
                if (resultDiv) {
                    resultDiv.innerHTML = `<div class="api-response ${response.ok ? 'success' : 'error'}">
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    </div>`;
                }
                
                if (response.ok) {
                    setTimeout(() => {
                        loadExistingData();
                        loadCommentsView();
                    }, 1000);
                } else {
                    if (!resultDiv) {
                        alert(`Ошибка: ${result.error || result.message || 'Неизвестная ошибка'}`);
                    }
                }
            } catch (error) {
                if (resultDiv) {
                    resultDiv.innerHTML = `<div class="api-response error">Ошибка сети: ${error.message}</div>`;
                } else {
                    alert(`Ошибка сети: ${error.message}`);
                }
            }
        }
        
        // Обработка формы лайка статьи
        document.getElementById('likePostForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            await likePost(formData.get('post_uuid'), formData.get('user_uuid'));
        });

        // Обработка формы лайка комментария
        document.getElementById('likeCommentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            await likeComment(formData.get('comment_uuid'), formData.get('user_uuid'));
        });
        
        // Функция переключения вкладок
        function showTab(tabName) {
            // Скрыть все вкладки
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Убрать активный класс у всех кнопок
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Показать выбранную вкладку
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Добавить активный класс к кнопке
            event.target.classList.add('active');
            
            // Загрузить данные для вкладки
            if (tabName === 'posts') {
                loadPostsView();
            } else if (tabName === 'comments') {
                loadCommentsView();
                loadDataForCommentForm();
            }
        }

        // Загрузка представления статей
        async function loadPostsView() {
            const container = document.getElementById('postsView');
            container.innerHTML = '<div class="loading">Загрузка статей...</div>';
            
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
                
                const data = await response.json();
                
                if (data.success) {
                    let html = '<div class="posts-grid-view">';
                    
                    data.posts.forEach(post => {
                        const postLikes = data.post_likes.filter(like => like.post_uuid === post.uuid);
                        const likesCount = postLikes.length;
                        
                        // Найти автора
                        const author = data.users.find(u => u.uuid === post.author_uuid);
                        const authorName = author ? `${author.first_name} ${author.last_name} (@${author.username})` : 'Неизвестен';
                        
                        html += `
                            <div class="post-card-view">
                                <div class="post-card-header">
                                    <h3>${escapeHtml(post.title)}</h3>
                                    <div class="post-author-info">👤 ${escapeHtml(authorName)}</div>
                                </div>
                                <div class="post-card-body">
                                    <p>${escapeHtml(post.text)}</p>
                                </div>
                                <div class="post-card-footer">
                                    <div class="likes-section">
                                        <div class="likes-count-badge">❤️ ${likesCount} лайков</div>
                                        ${likesCount > 0 ? `
                                            <div class="likes-list">
                                                <strong>Лайки от:</strong>
                                                ${postLikes.map(like => {
                                                    const likeUser = data.users.find(u => u.uuid === like.user_uuid);
                                                    return likeUser ? 
                                                        `<span class="like-user">${likeUser.first_name} ${likeUser.last_name}</span>` : 
                                                        '';
                                                }).filter(Boolean).join(', ')}
                                            </div>
                                        ` : '<div class="no-likes">Пока нет лайков</div>'}
                                    </div>
                                    <div class="post-actions">
                                        <select id="userSelect_${post.uuid}" class="user-select">
                                            <option value="">Выберите пользователя</option>
                                            ${data.users.map(user => 
                                                `<option value="${user.uuid}">${user.first_name} ${user.last_name} (@${user.username})</option>`
                                            ).join('')}
                                        </select>
                                        <button onclick="likePostFromView('${post.uuid}')" class="btn btn-like-small">❤️ Лайк</button>
                                    </div>
                                    <small class="uuid-text">UUID: ${post.uuid}</small>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="api-response error">Ошибка загрузки данных</div>';
                }
            } catch (error) {
                container.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        // Загрузка представления комментариев
        async function loadCommentsView() {
            const container = document.getElementById('commentsView');
            container.innerHTML = '<div class="loading">Загрузка комментариев...</div>';
            
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
                
                const data = await response.json();
                
                if (data.success) {
                    let html = '<div class="comments-grid-view">';
                    
                    data.comments.forEach(comment => {
                        const commentLikes = data.comment_likes.filter(like => like.comment_uuid === comment.uuid);
                        const likesCount = commentLikes.length;
                        
                        // Найти автора комментария
                        const author = data.users.find(u => u.uuid === comment.author_uuid);
                        const authorName = author ? `${author.first_name} ${author.last_name} (@${author.username})` : 'Неизвестен';
                        
                        html += `
                            <div class="comment-card-view">
                                <div class="comment-card-header">
                                    <div class="comment-author-info">👤 ${escapeHtml(authorName)}</div>
                                    <div class="comment-post-info">📝 К статье: ${escapeHtml(comment.post_title || 'Неизвестна')}</div>
                                </div>
                                <div class="comment-card-body">
                                    <p>${escapeHtml(comment.text)}</p>
                                </div>
                                <div class="comment-card-footer">
                                    <div class="likes-section">
                                        <div class="likes-count-badge">❤️ ${likesCount} лайков</div>
                                        ${likesCount > 0 ? `
                                            <div class="likes-list">
                                                <strong>Лайки от:</strong>
                                                ${commentLikes.map(like => {
                                                    const likeUser = data.users.find(u => u.uuid === like.user_uuid);
                                                    return likeUser ? 
                                                        `<span class="like-user">${likeUser.first_name} ${likeUser.last_name}</span>` : 
                                                        '';
                                                }).filter(Boolean).join(', ')}
                                            </div>
                                        ` : '<div class="no-likes">Пока нет лайков</div>'}
                                    </div>
                                    <div class="comment-actions">
                                        <select id="userSelectComment_${comment.uuid}" class="user-select">
                                            <option value="">Выберите пользователя</option>
                                            ${data.users.map(user => 
                                                `<option value="${user.uuid}">${user.first_name} ${user.last_name} (@${user.username})</option>`
                                            ).join('')}
                                        </select>
                                        <button onclick="likeCommentFromView('${comment.uuid}')" class="btn btn-like-small">❤️ Лайк</button>
                                    </div>
                                    <small class="uuid-text">UUID: ${comment.uuid}</small>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="api-response error">Ошибка загрузки данных</div>';
                }
            } catch (error) {
                container.innerHTML = `<div class="api-response error">Ошибка: ${error.message}</div>`;
            }
        }

        // Лайк статьи из представления
        async function likePostFromView(postUuid) {
            const select = document.getElementById(`userSelect_${postUuid}`);
            const userUuid = select.value;
            
            if (!userUuid) {
                alert('Выберите пользователя для лайка');
                return;
            }
            
            await likePost(postUuid, userUuid);
            setTimeout(() => loadPostsView(), 1000);
        }

        // Лайк комментария из представления
        async function likeCommentFromView(commentUuid) {
            const select = document.getElementById(`userSelectComment_${commentUuid}`);
            const userUuid = select.value;
            
            if (!userUuid) {
                alert('Выберите пользователя для лайка');
                return;
            }
            
            await likeComment(commentUuid, userUuid);
            setTimeout(() => loadCommentsView(), 1000);
        }

        // Загрузка данных для формы создания комментария
        async function loadDataForCommentForm() {
            try {
                let apiUrl = '/api/get_data.php';
                let response = await fetch(apiUrl);
                
                if (response.status === 404) {
                    apiUrl = 'api/get_data.php';
                    response = await fetch(apiUrl);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return;
                }
                
                const data = await response.json();
                
                if (data.success) {
                    // Заполнить список статей
                    const postSelect = document.getElementById('commentPostSelect');
                    if (postSelect) {
                        postSelect.innerHTML = '<option value="">Выберите статью</option>';
                        data.posts.forEach(post => {
                            const option = document.createElement('option');
                            option.value = post.uuid;
                            option.textContent = post.title;
                            postSelect.appendChild(option);
                        });
                    }
                    
                    // Заполнить список пользователей
                    const authorSelect = document.getElementById('commentAuthorSelect');
                    if (authorSelect) {
                        authorSelect.innerHTML = '<option value="">Выберите автора</option>';
                        data.users.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.uuid;
                            option.textContent = `${user.first_name} ${user.last_name} (@${user.username})`;
                            authorSelect.appendChild(option);
                        });
                    }
                }
            } catch (error) {
                console.error('Ошибка загрузки данных для формы комментария:', error);
            }
        }

        // Создание комментария
        async function createComment(postUuid, authorUuid, text) {
            const resultDiv = document.getElementById('createCommentResult');
            if (resultDiv) {
                resultDiv.innerHTML = '<div class="loading">Создание комментария...</div>';
            }

            try {
                let apiUrl = '/api/comments/create.php';
                let response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        post_uuid: postUuid,
                        author_uuid: authorUuid,
                        text: text
                    })
                });

                if (response.status === 404) {
                    apiUrl = 'api/comments/create.php';
                    response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            post_uuid: postUuid,
                            author_uuid: authorUuid,
                            text: text
                        })
                    });
                }

                let result;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    try {
                        result = await response.json();
                    } catch (jsonError) {
                        const text = await response.text();
                        if (resultDiv) {
                            resultDiv.innerHTML = `<div class="api-response error">
                                <strong>Status:</strong> ${response.status}<br>
                                <strong>Ошибка парсинга JSON:</strong> ${jsonError.message}<br>
                                <strong>Ответ сервера:</strong><br>
                                <pre>${escapeHtml(text.substring(0, 500))}</pre>
                            </div>`;
                        } else {
                            alert(`Ошибка: ${response.status}\n${text.substring(0, 200)}`);
                        }
                        return;
                    }
                } else {
                    const text = await response.text();
                    if (resultDiv) {
                        resultDiv.innerHTML = `<div class="api-response error">
                            <strong>Status:</strong> ${response.status}<br>
                            <strong>Ошибка:</strong> Сервер вернул не JSON ответ<br>
                            <pre>${escapeHtml(text.substring(0, 500))}</pre>
                        </div>`;
                    } else {
                        alert(`Ошибка: ${response.status}\n${text.substring(0, 200)}`);
                    }
                    return;
                }
                
                if (resultDiv) {
                    resultDiv.innerHTML = `<div class="api-response ${response.ok ? 'success' : 'error'}">
                        <strong>Status:</strong> ${response.status}<br>
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    </div>`;
                }
                
                if (response.ok) {
                    // Очистить форму
                    document.getElementById('createCommentForm').reset();
                    // Обновить список комментариев
                    setTimeout(() => {
                        loadCommentsView();
                        loadExistingData();
                    }, 1000);
                } else {
                    if (!resultDiv) {
                        alert(`Ошибка: ${result.error || result.message || 'Неизвестная ошибка'}`);
                    }
                }
            } catch (error) {
                if (resultDiv) {
                    resultDiv.innerHTML = `<div class="api-response error">Ошибка сети: ${error.message}</div>`;
                } else {
                    alert(`Ошибка сети: ${error.message}`);
                }
            }
        }

        // Обработка формы создания комментария
        document.getElementById('createCommentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const postUuid = formData.get('post_uuid');
            const authorUuid = formData.get('author_uuid');
            const text = formData.get('text');
            
            if (!postUuid || !authorUuid || !text) {
                alert('Заполните все поля!');
                return;
            }
            
            await createComment(postUuid, authorUuid, text);
        });

        // Загрузка данных при загрузке страницы
        window.addEventListener('DOMContentLoaded', function() {
            loadExistingData();
        });
    </script>
</body>
</html>

