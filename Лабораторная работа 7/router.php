<?php
// Router для встроенного PHP сервера
// Использование: php -S localhost:8000 router.php

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Если это запрос к API
if (strpos($path, '/api/') === 0) {
    // Специальная обработка для get_data.php
    if ($path === '/api/get_data.php' || strpos($path, '/api/get_data') !== false) {
        require __DIR__ . '/api/get_data.php';
        return true;
    }
    // Специальная обработка для прямых endpoint-файлов
    if ($path === '/api/posts/like.php') {
        require __DIR__ . '/api/posts/like.php';
        return true;
    }
    if ($path === '/api/posts/create.php') {
        require __DIR__ . '/api/posts/create.php';
        return true;
    }
    if ($path === '/api/comments/like.php') {
        require __DIR__ . '/api/comments/like.php';
        return true;
    }
    if ($path === '/api/comments/create.php') {
        require __DIR__ . '/api/comments/create.php';
        return true;
    }
    require __DIR__ . '/api/index.php';
    return true;
}

// Если это запрос к статическим файлам
if (file_exists(__DIR__ . $path) && $path !== '/') {
    return false; // Отдать файл как есть
}

// Все остальные запросы на index.php
require __DIR__ . '/index.php';
return true;


