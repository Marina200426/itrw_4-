<?php

/**
 * Endpoint для получения логов
 * GET /api/get_logs.php
 */

ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Обработчик фатальных ошибок
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Fatal error: ' . $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        ob_end_flush();
        exit;
    }
});

try {
    $logFile = __DIR__ . '/../logs/app.log';
    
    if (!file_exists($logFile)) {
        echo json_encode([
            'success' => true,
            'logs' => [],
            'message' => 'Log file does not exist yet'
        ]);
        ob_end_flush();
        exit;
    }
    
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $logs = [];
    
    foreach ($lines as $line) {
        // Парсинг формата: [timestamp] [level] message
        if (preg_match('/^\[([^\]]+)\] \[([^\]]+)\] (.+)$/', $line, $matches)) {
            $logs[] = [
                'timestamp' => $matches[1],
                'level' => $matches[2],
                'message' => $matches[3]
            ];
        }
    }
    
    // Обратный порядок (новые логи первыми)
    $logs = array_reverse($logs);
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'logs' => $logs,
        'count' => count($logs)
    ]);
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

ob_end_flush();
exit;

