<?php
// control-panel.php
ini_set('default_charset', 'UTF-8');
$configFile = "config.json";

// Servidores de relógio atômico
$clockServers = [
    "https://worldtimeapi.org/api/timezone/America/Sao_Paulo",
    "https://timeapi.io/api/Time/current/zone?timeZone=America/Sao_Paulo",
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

// Lê ou cria config.json
if (file_exists($configFile)) {
    $json = file_get_contents($configFile);
    $dados = json_decode($json, true);
    if (!$dados) $dados = $defaultConfig;
} else {
    $dados = $defaultConfig;
}

$mensagem = "";

// Atualizar data e hora
if (isset($_POST["atualizar_horario"])) {
    $atualizado = false;
    foreach ($clockServers as $server) {
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
                    $mensagem = "✔ Arquivo atualizado com sucesso!";
                    $atualizado = true;
                } else {
                    $mensagem = "❌ Erro ao gravar o arquivo JSON.";
                }
                break;
            }
        }
    }
    if (!$atualizado && empty($mensagem)) {
        $mensagem = "❌ Não foi possível obter os dados dos servidores.";
    }
}

function formatarDataHora($dataHoraISO) {
    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $dataHoraISO);
    return $dt ? $dt->format('Ymd\THis') : null;
}

// Atualizar clima e tempo
if (isset($_POST["atualizar_clima"])) {
    $lat = -23.5505;  // Latitude de São Paulo
    $long = -46.6333; // Longitude de São Paulo
    $atualizado = false;

    $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$long}&hourly=temperature_2m,precipitation,cloudcover&current_weather=true&timezone=America%2FSao_Paulo";

    $response = @file_get_contents($url);

    if ($response !== false) {
        $dadosClima = json_decode($response, true);
        if ($dadosClima && isset($dadosClima['current_weather'])) {
            $climaAtual = $dadosClima['current_weather'];
            $i = array_search($climaAtual['time'], $dadosClima['hourly']['time']);

            $ceuatual        = $dadosClima['hourly']['cloudcover'][$i] ?? null;
            $temperaturaatual = $climaAtual['temperature'] ?? null;
            $chuvaatual      = $dadosClima['hourly']['precipitation'][$i] ?? null;
            $atualizaclima   = formatarDataHora($climaAtual['time']) ?? null;

            // Lê o arquivo JSON atual
            $conteudo = file_exists($configFile) ? json_decode(file_get_contents($configFile), true) : [];

            $conteudo["siteconfig"]["nebulosidadeatual"]        = $ceuatual;
            $conteudo["siteconfig"]["temperaturaatual"]         = $temperaturaatual;
            $conteudo["siteconfig"]["chuvaatual"]               = $chuvaatual;
            $conteudo["siteconfig"]["atualizaclima"]            = $atualizaclima;

            if (file_put_contents($configFile, json_encode($conteudo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
                $mensagem = "✔ Clima atualizado com sucesso!";
                $atualizado = true;
            } else {
                $mensagem = "❌ Erro ao gravar os dados de clima no JSON.";
            }
        }
    }

    if (!$atualizado && empty($mensagem)) {
        $mensagem = "❌ Não foi possível obter os dados do clima.";
    }
}

// Salvar configurações
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

function formatarData($datetimeStr) {
    if (empty($datetimeStr)) return '';
    $dt = DateTime::createFromFormat('Ymd\THis', $datetimeStr);
    return $dt ? $dt->format('d/m/Y H:i:s') : $datetimeStr;
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
            
<!-- Coluna 3 -->
<div class="column">
    <div class="datahora">
        <h2>Atualizar Data e Hora</h2>
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
            <p><strong>Última atualização:</strong> Ainda não houve atualização.</p>
        <?php endif; ?>
        <form method="post">
            <button type="submit" name="atualizar_horario">Atualizar data e hora</button>
        </form>
    </div>
    <div class="tempoclima">
        <h2>Atualizar Clima e Tempo</h2>
        <?php if (!empty($dados["siteconfig"]["atualizaclima"])): ?>
            <div class="info">
                <strong>Última Atualização de Clima/Tempo:</strong><br>
                <?= formatarData($dados['siteconfig']['atualizaclima']); ?><br>
                <strong>Servidor Utilizado:</strong><br>https://api.open-meteo.com/v1/forecast<br>
                <strong>Arquivo gravado:</strong><br>
                <?= $configFile; ?>
            </div>
        <?php else: ?>
            <p><strong>Última atualização:</strong> Ainda não houve atualização.</p>
        <?php endif; ?>
        <form method="post">
            <button type="submit" name="atualizar_clima">Atualizar clima e tempo</button>
        </form>
    </div>
</div>

</body>
</html>
