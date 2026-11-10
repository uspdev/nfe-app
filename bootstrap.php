<?php

$baseDir = __DIR__;

// Carrega arquivo de configuração
if (!is_file($baseDir . '/config.php')) {
    if (PHP_SAPI !== 'cli') {
        http_response_code(500);
    }
    die('Configure a aplicação antes de usar! Copie config.sample.php para config.php.');
}
require_once $baseDir . '/config.php';

// Carrega autoload do Composer com fallback para desenvolvimento local
if (file_exists($baseDir . '/vendor/autoload.php')) {
    require_once $baseDir . '/vendor/autoload.php';
} elseif (file_exists($baseDir . '/../nfe-ws-consumer/src/NfeWsConsumer.php')) {
    require_once $baseDir . '/../nfe-ws-consumer/src/NfeWsConsumer.php';
}
