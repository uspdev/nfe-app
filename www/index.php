<?php
require_once __DIR__ . '/../bootstrap.php';

$uri = $_SERVER["REQUEST_URI"] ?? '/';

use Uspdev\Nfe\NfeWsConsumer;

$url = isset($_POST['url']) && trim($_POST['url']) !== '' ? trim($_POST['url']) : ($ws_url ?? '');
$apiKey = $ws_api_key ?? '';
$xml = $_POST['xml'] ?? '';
$chave = $_POST['chave'] ?? '';
$action = $_POST['action'] ?? '';

$sslVerify = isset($ws_ssl_verify) ? (bool)$ws_ssl_verify : false;
$statusRes = null;
$res = null;
$erroGeral = null;

function hasErro($val) {
    return is_string($val) && strncmp($val, 'Erro', 4) === 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!class_exists('Uspdev\Nfe\NfeWsConsumer')) {
        $erroGeral = 'A biblioteca Uspdev\Nfe\NfeWsConsumer não foi encontrada. Execute "composer install".';
    } else {
        $consumer = new NfeWsConsumer($url, $apiKey, 0, $sslVerify);

        if ($action === 'status') {
            $statusRes = $consumer->status();
            if ($statusRes === false || empty($statusRes)) {
                $err = error_get_last();
                $detalhe = !empty($err['message']) ? ': ' . htmlspecialchars($err['message']) : '';
                $statusRes = '<span class="red">Erro ao consultar status do webservice' . $detalhe . '</span>';
            }
        } elseif ($action === 'enviar') {
            if (empty(trim($xml)) && empty(trim($chave))) {
                $erroGeral = 'Informe o XML ou a chave de 44 dígitos da NF-e.';
            } else {
                $ret = !empty(trim($chave)) && empty(trim($xml))
                    ? $consumer->consultaChave(trim($chave))
                    : $consumer->consultaXML($xml);
                if (is_array($ret)) {
                    $res = $ret;
                } else {
                    $erroGeral = 'Resposta inválida do webservice: ' . htmlspecialchars((string)$ret);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>NFE</title>
    <meta charset="utf-8">
    <base href="<?php echo htmlspecialchars($uri) ?>"/>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="lib/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .red {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2>Consulta NFe</h2>

                <?php if (!empty($erroGeral)): ?>
                    <div class="alert alert-danger">
                        <?= $erroGeral ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <p>Webservice</p>
                    <input type="text" name="url" size="50" value="<?= htmlspecialchars($url) ?>" />
                    <button type="submit" name="action" value="status" class="btn btn-default btn-sm">
                        <i class="fa fa-refresh"></i> Consultar Status
                    </button>

                    <?php if ($statusRes !== null): ?>
                        <div style="margin-top: 10px;" class="well well-sm">
                            <?php if (is_array($statusRes)): ?>
                                <?php
                                $desatualizado = !empty($statusRes['modo_compatibilidade'])
                                    || (!empty($statusRes['versao_em_uso']) && !empty($statusRes['versao_corrente']) && $statusRes['versao_em_uso'] !== $statusRes['versao_corrente']);
                                $suggestedUrl = preg_replace('#/v\d+$#', '', rtrim($url, '/')) . '/' . ($statusRes['versao_corrente'] ?? 'v1');
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div><strong>Certificado Digital</strong></div>
                                        <?php if (isset($statusRes['erro'])): ?>
                                            <div class="red">Erro: <?= htmlspecialchars($statusRes['erro']) ?></div>
                                        <?php else: ?>
                                            <div><strong><?= htmlspecialchars($statusRes['cnpj'] ?? '') ?></strong> - <?= htmlspecialchars($statusRes['empresa'] ?? '') ?></div>
                                            <div>Validade: <?= htmlspecialchars($statusRes['validade'] ?? '') ?></div>
                                            <div class="<?= ($statusRes['expirado'] ?? false) ? 'red' : 'green' ?>">
                                                <?= htmlspecialchars($statusRes['mensagem'] ?? '') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div><strong>Informações da API</strong></div>
                                        <div>Versão em uso: <strong><?= htmlspecialchars($statusRes['versao_em_uso'] ?? '-') ?></strong></div>
                                        <div>Versão mais recente: <strong><?= htmlspecialchars($statusRes['versao_corrente'] ?? '-') ?></strong></div>
                                        <?php if ($desatualizado): ?>
                                            <div class="alert alert-warning" style="margin-top: 8px; margin-bottom: 0;">
                                                <strong>Atenção:</strong> Configure a URL do servidor (<code>$srv</code>) para usar a versão mais recente:
                                                <code><?= htmlspecialchars($suggestedUrl) ?></code>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div><?= $statusRes ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <br><br>

                    <p>Cole o xml aqui</p>
                    <textarea name="xml" rows="6" cols="100"><?= htmlspecialchars($xml) ?></textarea>

                    <p>Ou a chave (somente numeros - 44 digitos)</p>
                    <input type="text" name="chave" size="50" value="<?= htmlspecialchars($chave) ?>" />
                    <br/><br/>
                    <button type="submit" name="action" value="enviar" class="btn btn-primary">Enviar</button>
                </form>
            </div>
        </div>

        <?php if (!empty($res)): ?>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-6">
                    <div>
                        <br/>
                        <b>Retorno</b><br/>

                        <?php if (!empty($res['chave'])): ?>
                            <div>
                                Chave: <b><?= htmlspecialchars($res['chave']) ?></b>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($res['xml']) && is_array($res['xml'])): ?>
                            <br/>
                            <div><b>XML</b></div>
                            <?php foreach ($res['xml'] as $key => $val): ?>
                                <div>
                                    <?= htmlspecialchars($key) ?>:
                                    <span class="<?= hasErro($val) ? 'red' : '' ?>"><b><?= htmlspecialchars(is_array($val) ? json_encode($val) : (string)$val) ?></b></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($res['prot']) && is_array($res['prot'])): ?>
                            <br/>
                            <b>Protocolo</b><br/>
                            <?php foreach ($res['prot'] as $key => $val): ?>
                                <?php if ($key !== 'raw' && $key !== 'eventos'): ?>
                                    <div>
                                        <?= htmlspecialchars($key) ?>:
                                        <span class="<?= hasErro($val) ? 'red' : '' ?>"><b><?= htmlspecialchars(is_array($val) ? json_encode($val) : (string)$val) ?></b></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if (!empty($res['prot']['eventos']) && is_array($res['prot']['eventos'])): ?>
                                <?php foreach ($res['prot']['eventos'] as $ev): ?>
                                    <div>
                                        Evento: (<?= htmlspecialchars($ev['dhEvento'] ?? '') ?>) <?= htmlspecialchars($ev['tpEvento'] ?? '') ?> - <?= htmlspecialchars($ev['descEvento'] ?? '') ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <?php if (!empty($res['sefaz']) && is_array($res['sefaz'])): ?>
                        <br/>
                        <div><b>SEFAZ</b></div>
                        <?php foreach ($res['sefaz'] as $key => $val): ?>
                            <div>
                                <?= htmlspecialchars($key) ?>:
                                <span class="<?= hasErro($val) ? 'red' : '' ?>"><b><?= htmlspecialchars(is_array($val) ? json_encode($val) : (string)$val) ?></b></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($res['url']) && is_array($res['url'])): ?>
                        <br/>
                        <div><b>Links</b></div>
                        <?php foreach ($res['url'] as $key => $val): ?>
                            <?php $linkHref = is_file(__DIR__ . '/download.php') ? 'download.php?url=' . urlencode($val) : $val; ?>
                            <div>
                                <?= htmlspecialchars($key) ?>: <a href="<?= htmlspecialchars($linkHref) ?>" target="_blank"><?= htmlspecialchars($key) ?></a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="lib/jquery-2.1.1.min.js"></script>
    <script src="lib/bootstrap.min.js"></script>
</body>
</html>
