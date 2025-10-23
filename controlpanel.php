<?php
// control-panel.php
ini_set('default_charset', 'UTF-8');
$configFile = "config.json";
$location = "America/Sao_Paulo";
$lat = -23.5505;
$long = -46.6333;
$mensagem_sucesso = "";  // inicializa apenas, sem chamar funções

$clockServers = [
    "https://worldtimeapi.org/api/timezone/",
    "https://timeapi.io/api/Time/current/zone?timeZone=",
    "https://worldclockapi.com/api/json/br/now"
];

// Configuração padrão
$defaultConfig = [
    "siteconfig" => [
        "sitename" => "Gazeta de Notícias",
        "siteslogan" => "O que você precisa saber está aqui",
        "sitelogo" => "img/logo1.png",
        "sitecategory" => "News Portal",
        "sitetype" => "",
        "pagetype" => "homepage",
        "sitecopyright" => "&copy; 2025 - Fábio de Almeida Martins",
        "siteowner" => "Fábio de Almeida Martins",
        "sitedeveloper" => "Fábio de Almeida Martins",
        "siteemail" => "fabiomartins01@gmail.com",
        "cpfcnpj" => "",
        "colunas" => "4",
        "gappercent" => "2",
        "cardheight" => "",
        "cardpadding" => "5px",
        "favfonttype" => "",
        "ano" => "I",
        "numero" => "1",
        "edicao" => "1",
        "datetime" => "",
        "lastclockserver" => "",
        "temperaturaatual" => "",
        "nebulosidadeatual" => "",
        "chuvaatual" => "",
        "atualizaclima" => ""
    ]
];

// Lê config.json ou cria novo
$dados = file_exists($configFile)
    ? json_decode(file_get_contents($configFile), true)
    : $defaultConfig;
if (!$dados) $dados = $defaultConfig;

$mensagem = "";

// =========================================================
// Funções: Atualiza Data e Hora + Atualiza Clima e Tempo
// =========================================================
function atualizaDataHora($location) {
    global $clockServers, $configFile, $dados;

    $clockServersCompletos = array_map(function($url) use ($location) {
        if ((substr($url, -1) === '/') || (strpos($url, '=') !== false)) {
            return $url . $location;
        }
        return $url;
    }, $clockServers);

    foreach ($clockServersCompletos as $server) {
        $response = @file_get_contents($server);
        if ($response !== false) {
            $dadosServ = json_decode($response, true);
            if ($dadosServ) {
                if (isset($dadosServ['datetime'])) {
                    $datetime = substr($dadosServ['datetime'], 0, 19);
                } elseif (isset($dadosServ['dateTime'])) {
                    $datetime = substr($dadosServ['dateTime'], 0, 19);
                } elseif (isset($dadosServ['currentDateTime'])) {
                    $datetime = substr($dadosServ['currentDateTime'], 0, 19);
                } else {
                    continue;
                }

                $formattedJSON = str_replace(['-', ':'], '', $datetime);
                $dados["siteconfig"]["datetime"] = $formattedJSON;
                $dados["siteconfig"]["lastclockserver"] = $server;

                if (file_put_contents($configFile, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
                    $dados = json_decode(file_get_contents($configFile), true); // recarrega dados
                    return ["success" => true, "message" => "✔ Data e hora atualizadas com sucesso!"];
                } else {
                    return ["success" => false, "message" => "❌ Erro ao gravar o arquivo JSON."];
                }
            }
        }
    }
    return ["success" => false, "message" => "❌ Nenhum servidor de horário respondeu corretamente."];
}

function atualizaClimaTempo($lat, $long) {
    global $configFile, $dados, $location;

    $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$long}&hourly=temperature_2m,precipitation,cloudcover&current_weather=true&timezone=" . urlencode($location);

    $response = @file_get_contents($url);
    if ($response === false) {
        return ["success" => false, "message" => "❌ Erro ao acessar servidor de clima."];
    }

    $dadosClima = json_decode($response, true);
    if (!$dadosClima || !isset($dadosClima['current_weather'])) {
        return ["success" => false, "message" => "❌ Dados de clima inválidos."];
    }

    $climaAtual = $dadosClima['current_weather'];
    $i = array_search($climaAtual['time'], $dadosClima['hourly']['time']);

    $ceuatual         = $dadosClima['hourly']['cloudcover'][$i] ?? null;
    $temperaturaatual = $climaAtual['temperature'] ?? null;
    $chuvaatual       = $dadosClima['hourly']['precipitation'][$i] ?? null;
    $atualizaclima    = formatarDataHora($climaAtual['time']) ?? null;

    $dados["siteconfig"]["nebulosidadeatual"] = $ceuatual;
    $dados["siteconfig"]["temperaturaatual"] = $temperaturaatual;
    $dados["siteconfig"]["chuvaatual"] = $chuvaatual;
    $dados["siteconfig"]["atualizaclima"] = $atualizaclima;

    if (file_put_contents($configFile, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
        $dados = json_decode(file_get_contents($configFile), true); // recarrega dados
        return ["success" => true, "message" => "✔ Clima atualizado com sucesso!"];
    } else {
        return ["success" => false, "message" => "❌ Erro ao gravar os dados de clima."];
    }
}

// =========================================================
// Quando o botão unificado for clicado
// =========================================================
if (isset($_POST["atualizar_horario_clima"])) {
    $res1 = atualizaDataHora($location);
    $mensagem = $res1["message"];

    if ($res1["success"]) {
        $res2 = atualizaClimaTempo($lat, $long);
        $mensagem .= "<br>" . $res2["message"];
    }
    if (!empty($res1["success"]) && (!isset($res2) || $res2["success"])) {
        $mensagem_sucesso = "✔ Atualização concluída com sucesso!";
    }
}

// =========================================================
// Funções auxiliares
// =========================================================
function formatarDataHora($dataHoraISO) {
    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $dataHoraISO);
    return $dt ? $dt->format('Ymd\THis') : null;
}

function formatarData($datetimeStr) {
    if (empty($datetimeStr)) return '';
    $dt = DateTime::createFromFormat('Ymd\THis', $datetimeStr);
    return $dt ? $dt->format('d/m/Y H:i:s') : $datetimeStr;
}

// =========================================================
// Salvar Configurações (mantido igual)
// =========================================================
if (isset($_POST["salvar_config"])) {
    $campos = [
        "sitename","siteslogan","sitelogo","sitecategory","sitetype","pagetype",
        "sitecopyright","siteowner","sitedeveloper","siteemail","cpfcnpj",
        "colunas","gappercent","cardheight","cardpadding","favfonttype","ano","numero","edicao"
    ];

    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            $dados["siteconfig"][$campo] = trim($_POST[$campo]);
        }
    }

    if (file_put_contents($configFile, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
        $mensagem = "✔ Configurações salvas com sucesso!";
    } else {
        $mensagem = "❌ Erro ao salvar configurações.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel de Controle</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #fff;
    color: #333;
    margin: 0;
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    height: 100vh;
    box-sizing: border-box;
    overflow: hidden;
}
h1 {
    grid-column: 1 / span 3;
    text-align: center;
    margin: 0 0 10px 0;
}
.column {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px 20px;
    overflow: hidden;
}
label {
    display: block;
    margin-bottom: 3px;
    font-weight: bold;
    font-size: 13px;
}
input[type=text], input[type=email], select {
    width: 100%;
    padding: 6px;
    margin-bottom: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 13px;
}
button {
    width: 100%;
    padding: 8px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
button:hover { background: #0056b3; }
.info {
    font-size: 13px;
    color: #444;
    background: #eef;
    padding: 10px;
    border-radius: 6px;
}
.success { color: green; }
.sucesso {
    color: #2e7d32;
    background-color: #e8f5e9;
    border: 1px solid #c8e6c9;
    padding: 8px 10px;
    border-radius: 6px;
    margin-top: 10px;
    font-weight: bold;
    text-align: center;
}
.error { color: red; }
</style>
</head>
<body>
<h1>Painel de Controle</h1>

<!-- Coluna 1 -->
<form class="column" method="post">
    <label>Nome do site:</label>
    <input type="text" name="sitename" value="<?= htmlspecialchars($dados['siteconfig']['sitename']) ?>">

    <label>Slogan do site:</label>
    <input type="text" name="siteslogan" value="<?= htmlspecialchars($dados['siteconfig']['siteslogan']) ?>">

    <label>Logotipo:</label>
    <input type="text" name="sitelogo" value="<?= htmlspecialchars($dados['siteconfig']['sitelogo']) ?>">

    <label>Categoria:</label>
    <input type="text" name="sitecategory" value="<?= htmlspecialchars($dados['siteconfig']['sitecategory']) ?>">

    <label>Tipo de site:</label>
    <input type="text" name="sitetype" value="<?= htmlspecialchars($dados['siteconfig']['sitetype']) ?>">

    <label>Tipo de página:</label>
    <input type="text" name="pagetype" value="<?= htmlspecialchars($dados['siteconfig']['pagetype']) ?>">

    <label>Direitos:</label>
    <input type="text" name="sitecopyright" value="<?= htmlspecialchars($dados['siteconfig']['sitecopyright']) ?>">

    <label>Proprietário:</label>
    <input type="text" name="siteowner" value="<?= htmlspecialchars($dados['siteconfig']['siteowner']) ?>">

    <label>Desenvolvedor:</label>
    <input type="text" name="sitedeveloper" value="<?= htmlspecialchars($dados['siteconfig']['sitedeveloper']) ?>">

    <label>Email:</label>
    <input type="email" name="siteemail" value="<?= htmlspecialchars($dados['siteconfig']['siteemail']) ?>">

    <label>CPF/CNPJ:</label>
    <input type="text" name="cpfcnpj" value="<?= htmlspecialchars($dados['siteconfig']['cpfcnpj']) ?>">

</form>

<!-- Coluna 2 -->
<form class="column" method="post">
    <label>Número de colunas:</label>
    <select name="colunas">
        <?php for ($i=1;$i<=10;$i++): ?>
        <option value="<?= $i ?>" <?= ($dados['siteconfig']['colunas']==$i?'selected':'') ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label>Medianiz ou margem entre os cards (%):</label>
    <input type="text" name="gappercent" value="<?= htmlspecialchars($dados['siteconfig']['gappercent']) ?>">

    <label>Altura dos cards:</label>
    <input type="text" name="cardheight" value="<?= htmlspecialchars($dados['siteconfig']['cardheight']) ?>">

    <label>Margem interna (de conteúdo) dos cards:</label>
    <input type="text" name="cardpadding" value="<?= htmlspecialchars($dados['siteconfig']['cardpadding']) ?>">

    <label>Fonte padrão:</label>
    <input type="text" name="favfonttype" value="<?= htmlspecialchars($dados['siteconfig']['favfonttype']) ?>">

    <label>Ano:</label>
    <input type="text" name="ano" value="<?= htmlspecialchars($dados['siteconfig']['ano']) ?>">

    <label>Número:</label>
    <input type="text" name="numero" value="<?= htmlspecialchars($dados['siteconfig']['numero']) ?>">

    <label>Edição:</label>
    <input type="text" name="edicao" value="<?= htmlspecialchars($dados['siteconfig']['edicao']) ?>">

    <button type="submit" name="salvar_config">Salvar alterações</button>
    <button type="button" onclick="window.close()" style="margin-top:8px;background:#6c757d;">Cancelar</button>
</form>
            
<!-- Coluna 3 unificada -->
<div class="column">
    <h2>Atualizar Data, Hora e Clima</h2>

    <!-- Informações de Data/Hora -->
    <?php if (!empty($dados["siteconfig"]["datetime"])): ?>
        <div class="info">
            <strong>Última Data Atualizada:</strong><br>
            <?= formatarData($dados['siteconfig']['datetime']); ?><br>
            <strong>Servidor Utilizado:</strong><br>
            <?= $dados['siteconfig']['lastclockserver'] ?: '-'; ?><br>
            <strong>Arquivo gravado:</strong><br>
            <?= $configFile; ?>
        </div>
    <?php else: ?>
        <p><strong>Última atualização:</strong> Ainda não houve atualização de data/hora.</p>
    <?php endif; ?>

    <!-- Informações de Clima/Tempo -->
    <?php if (!empty($dados["siteconfig"]["atualizaclima"])): ?>
        <div class="info" style="margin-top:10px;">
            <strong>Última Atualização de Clima/Tempo:</strong><br>
            <?= formatarData($dados['siteconfig']['atualizaclima']); ?><br>
            <strong>Servidor Utilizado:</strong><br>
            https://api.open-meteo.com/v1/forecast<br>
            <strong>Arquivo gravado:</strong><br>
            <?= $configFile; ?>
        </div>
    <?php else: ?>
        <p><strong>Última atualização:</strong> Ainda não houve atualização de clima/tempo.</p>
    <?php endif; ?>

    <!-- Botão unificado -->
    <form method="post" style="margin-top:10px;">
        <button type="submit" name="atualizar_horario_clima">Atualizar Data/Hora e Clima</button>
    </form>

    <!-- Mensagem de sucesso -->
    <?php if (!empty($mensagem_sucesso)): ?>
        <p class="sucesso"><?= $mensagem_sucesso; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
