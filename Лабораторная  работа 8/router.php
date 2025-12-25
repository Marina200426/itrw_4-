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
    // Специальная обработка для get_logs.php
    if ($path === '/api/get_logs.php' || strpos($path, '/api/get_logs') !== false) {
        require __DIR__ . '/api/get_logs.php';
        return true;
    }
    // Специальная обработка для тестовых endpoints
    if (strpos($path, '/api/test/') === 0) {
        $file = str_replace('/api/test/', '', $path);
        // Убираем query string если есть
        $file = strtok($file, '?');
        $filePath = __DIR__ . '/api/test/' . $file;
        if (file_exists($filePath) && is_file($filePath)) {
            require $filePath;
            return true;
        }
    }
    // Если файл API существует напрямую
    $apiFile = __DIR__ . $path;
    if (file_exists($apiFile) && is_file($apiFile)) {
        require $apiFile;
        return true;
    }
    return false;
}

// Если это запрос к статическим файлам (CSS, JS, изображения)
if ($path !== '/' && file_exists(__DIR__ . $path) && is_file(__DIR__ . $path)) {
    return false; // Отдать файл как есть
}

// Все остальные запросы на index.php
require __DIR__ . '/index.php';
return true;

