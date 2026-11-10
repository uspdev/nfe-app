<?php

require_once __DIR__ . '/../bootstrap.php';

use Uspdev\Nfe\NfeWsConsumer;

$targetUrl = $_GET['url'] ?? '';

if (empty($targetUrl)) {
    http_response_code(400);
    die('Parâmetro url não fornecido.');
}

// Garante que a URL pertença ao webservice configurado (proteção contra SSRF)
$normalizedTarget = rtrim($targetUrl, '/');
$normalizedWs = rtrim($ws_url, '/');

if (strpos($normalizedTarget, $normalizedWs) !== 0) {
    http_response_code(403);
    die('URL de destino não permitida.');
}

// Determina o tipo de conteúdo pela extensão
$extension = strtolower(pathinfo(parse_url($targetUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
$contentType = 'application/octet-stream';
if ($extension === 'pdf') {
    $contentType = 'application/pdf';
} elseif ($extension === 'xml') {
    $contentType = 'application/xml';
}

// Utiliza a classe consumidora para obter o arquivo via streaming autenticado
$sslVerify = isset($ws_ssl_verify) ? (bool)$ws_ssl_verify : false;
$consumer = new NfeWsConsumer($ws_url, $ws_api_key, 0, $sslVerify);
$content = $consumer->obterArquivo($targetUrl);

if ($content === false || empty($content)) {
    http_response_code(404);
    die('Arquivo não encontrado no webservice.');
}

// Entrega o arquivo para o navegador
$filename = basename(parse_url($targetUrl, PHP_URL_PATH));
header('Content-Type: ' . $contentType);
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));
echo $content;
exit;
