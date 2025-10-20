<?php
declare(strict_types=1);

$dateFile    = __DIR__ . '/config.json';
$contentFile = __DIR__ . '/conteudo.json';
$clockServers = [
    "https://worldtimeapi.org/api/timezone/America/Sao_Paulo",
    "https://timeapi.io/api/Time/current/zone?timeZone=America/Sao_Paulo",
    "https://worldclockapi.com/api/json/br/now"
];

$result = ["success" => false, "message" => ""];
$currentData = ["datetime" => "", "lastclockserver" => ""];

// Funções auxiliares
function readJsonFile(string $file): array {
    if (!file_exists($file)) return [];
    $raw = @file_get_contents($file);
    if ($raw === false || trim($raw) === '') return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function writeJsonFile(string $file, array $data): bool {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return @file_put_contents($file, $json, LOCK_EX) !== false;
}

function tryFetch(string $url): ?array {
    $ctx = stream_context_create([
        "http" => ["method" => "GET", "timeout" => 7, "ignore_errors" => true]
    ]);
    $resp = @file_get_contents($url, false, $ctx);
    if ($resp === false) return null;
    $data = json_decode($resp, true);
    return is_array($data) ? $data : null;
}

// Obtem a data e a hora atual através de relógios atômicos
function extractDateTime(array $data): ?DateTime {
    // worldtimeapi.org -> 'datetime'
    if (!empty($data['datetime'])) {
        try { return new DateTime($data['datetime']); } catch (Exception $e) {}
    }
    // timeapi.io -> 'dateTime' ou componentes separados
    if (!empty($data['dateTime'])) {
        try { return new DateTime($data['dateTime']); } catch (Exception $e) {}
    }
    if (isset($data['year'],$data['month'],$data['day'],$data['hour'],$data['minute'])) {
        $sec = $data['seconds'] ?? 0;
        $str = sprintf('%04d-%02d-%02dT%02d:%02d:%02d',
            (int)$data['year'], (int)$data['month'], (int)$data['day'],
            (int)$data['hour'], (int)$data['minute'], (int)$sec
        );
        try { return new DateTime($str); } catch (Exception $e) {}
    }
    // worldclockapi -> 'currentDateTime'
    if (!empty($data['currentDateTime'])) {
        try { return new DateTime($data['currentDateTime']); } catch (Exception $e) {}
    }
    return null;
}

function formatYmdTHis(DateTime $dt): string {
    return $dt->format('Ymd\THis');
}

// carrega datahora.json (se existir) para mostrar info na tela
if (file_exists($dateFile)) {
    $loaded = readJsonFile($dateFile);
    if (isset($loaded['datetime']))       $currentData['datetime'] = $loaded['datetime'];
    if (isset($loaded['lastclockserver'])) $currentData['lastclockserver'] = $loaded['lastclockserver'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $dtObj = null;
    $usedServer = '';

    foreach ($clockServers as $server) {
        $payload = tryFetch($server);
        if (!$payload) continue;
        $candidate = extractDateTime($payload);
        if ($candidate instanceof DateTime) {
            $dtObj = $candidate;
            $usedServer = $server;
            break;
        }
    }

    if (!$dtObj) {
        $result['success'] = false;
        $result['message'] = 'Não foi possível obter a hora dos servidores.';
    } else {
        // Força timezone de São Paulo, caso o endpoint venha sem tz clara
        try { $dtObj->setTimezone(new DateTimeZone('America/Sao_Paulo')); } catch (Exception $e) {}
        $formatted = formatYmdTHis($dtObj);

        // 1) Atualiza/cria datahora.json
        $okDatahora = writeJsonFile($dateFile, [
            'datetime' => $formatted,
            'lastclockserver' => $usedServer
        ]);

        // 2) Atualiza/cria conteudo.json sem duplicar na raiz
        $contentData = readJsonFile($contentFile);

        // Garante 'siteconfig' como array
        if (!isset($contentData['siteconfig']) || !is_array($contentData['siteconfig'])) {
            $contentData['siteconfig'] = [
                "sitename"         => "Gazeta de Notícias",
                "siteslogan"       => "O que você precisa SABER está aqui",
                "sitelogo"         => "img/logo1.png",
                "sitecategory"     => "News Portal",
                "sitetype"         => "",
                "pagetype"         => "homepage",
                "sitecopyright"    => "&copy; 2025 - Fábio de Almeida Martins - Direitos reservados",
                "siteowner"        => "Fabio de Almeida Martins",
                "sitedeveloper"    => "Fabio de Almeida Martins",
                "siteemail"        => "fabiomartins01@gmail.com",
                "cpfcnpj"          => "",
                "colunas"          => "4",
                "favfonttype"      => "",
                "datetime"         => "",
                "lastclockserver"  => ""
            ];
        }

        // MIGRA/REMOVE duplicatas na raiz (se existirem)
        foreach (['datetime','lastclockserver'] as $k) {
            if (isset($contentData[$k])) {
                // move para dentro de siteconfig
                $contentData['siteconfig'][$k] = $contentData[$k];
                unset($contentData[$k]);
            }
        }

        // Atualiza os campos corretos
        $contentData['siteconfig']['datetime'] = $formatted;
        $contentData['siteconfig']['lastclockserver'] = $usedServer;

        $okConteudo = writeJsonFile($contentFile, $contentData);

        if ($okDatahora && $okConteudo) {
            $result['success'] = true;
            $result['message'] = 'Arquivos atualizados com sucesso!';
            $currentData['datetime'] = $formatted;
            $currentData['lastclockserver'] = $usedServer;
        } else {
            $result['success'] = false;
            $result['message'] = 'Erro ao gravar um ou mais arquivos JSON.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Atualizar Data e Hora</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    :root{
        --bg:#fff; --fg:#222; --muted:#666; --accent:#0d6efd; --border:#e6e6e6; --ok:#0a7d33; --err:#b00020;
    }
    body{
        margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
        background:var(--bg); color:var(--fg); font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }
    .wrap{
        width:min(480px,90vw); padding:28px; border:1px solid var(--border); border-radius:14px;
        box-shadow:0 6px 24px rgba(0,0,0,.06);
    }
    h1{ font-size:1.25rem; margin:0 0 12px; text-align:center; }
    p.sub{ margin:0 0 18px; text-align:center; color:var(--muted); }
    form{ display:flex; gap:10px; justify-content:center; margin:6px 0 14px; }
    button{
        appearance:none; border:none; border-radius:10px; padding:12px 16px; font-weight:600; cursor:pointer;
        background:var(--accent); color:#fff;
    }
    button:hover{ filter:brightness(.92); }
    .msg{ text-align:center; margin:8px 0 10px; font-weight:600; }
    .msg.ok{ color:var(--ok); }
    .msg.err{ color:var(--err); }
    .info{
        margin-top:12px; font-size:.95rem; color:var(--muted);
        border-top:1px dashed var(--border); padding-top:12px;
        word-break: break-word;
    }
    .row{ display:flex; justify-content:space-between; gap:10px; margin:4px 0; }
    .label{ color:var(--muted); }
    .value{ color:var(--fg); font-weight:600; text-align:right; }
</style>
</head>
<body>
    <div class="wrap">
        <h1>Atualizar Data e Hora</h1>
        <p class="sub">Grava em <code>datahora.json</code> e em <code>conteudo.json → siteconfig</code>.</p>

        <form method="post">
            <button type="submit" name="atualizar">Atualizar agora</button>
        </form>

        <?php if ($result['message']): ?>
            <div class="msg <?= $result['success'] ? 'ok' : 'err' ?>">
                <?= htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="info">
            <div class="row">
                <div class="label">Última atualização:</div>
                <div class="value"><?= $currentData['datetime'] ?: '—' ?></div>
            </div>
            <div class="row">
                <div class="label">Servidor utilizado:</div>
                <div class="value"><?= $currentData['lastclockserver'] ?: '—' ?></div>
            </div>
        </div>
    </div>
</body>
</html>
