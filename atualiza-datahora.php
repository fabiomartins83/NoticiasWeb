<?php
// Nome do arquivo de configuração
$configFile = "config.json";

// Servidores de relógio atômico
$clockServers = [
    "https://worldtimeapi.org/api/timezone/America/Sao_Paulo",
    "https://timeapi.io/api/Time/current/zone?timeZone=America/Sao_Paulo",
    "https://worldclockapi.com/api/json/br/now"
];

// Modelo do arquivo config.json caso ainda não exista
$defaultConfig = [
    "siteconfig" => [
        "sitename" => "Gazeta de Notícias",
        "siteslogan" => "O que você precisa SABER está aqui",
        "sitelogo" => "img/logo1.png",
        "sitecategory" => "News Portal",
        "sitetype" => "",
        "pagetype" => "homepage",
        "sitecopyright" => "&copy; 2025 - Fábio de Almeida Martins - Direitos reservados",
        "siteowner" => "Fabio de Almeida Martins",
        "sitedeveloper" => "Fabio de Almeida Martins",
        "siteemail" => "fabiomartins01@gmail.com",
        "cpfcnpj" => "",
        "colunas" => "4",
        "favfonttype" => "",
        "datetime" => "",
        "lastclockserver" => ""
    ]
];

// Lê arquivo config.json se existir
if (file_exists($configFile)) {
    $json = file_get_contents($configFile);
    $dados = json_decode($json, true);
    if (!$dados) $dados = $defaultConfig;
} else {
    $dados = $defaultConfig;
}

// Atualiza data e hora quando o botão é clicado
$mensagem = '';
if (isset($_POST["atualizar"])) {
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

                // Formata para YYYYmmddThhmmss para gravar no JSON
                $formattedJSON = str_replace(['-', ':'], '', $datetime);

                // Atualiza os campos no JSON
                $dados["siteconfig"]["datetime"] = $formattedJSON;
                $dados["siteconfig"]["lastclockserver"] = $server;

                // Grava no arquivo
                if (file_put_contents($configFile, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
                    $mensagem = "✔ Arquivo atualizado com sucesso!";
                    $atualizado = true;
                } else {
                    $mensagem = "❌ Erro ao gravar o arquivo JSON.";
                }
                break; // sai do loop após sucesso
            }
        }
    }
    if (!$atualizado && empty($mensagem)) {
        $mensagem = "❌ Não foi possível obter os dados dos servidores.";
    }
}

// Função para formatar data legível
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
<title>Atualização de Data e Hora</title>
<style>
    body { 
        font-family: Arial, sans-serif; 
        background: #fff; 
        color: #333; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        min-height: 100vh; 
        margin: 20px; 
    }
    h1 { margin-bottom: 20px; }
    .card {
        border: 1px solid #ddd; 
        border-radius: 8px; 
        padding: 20px; 
        max-width: 450px; 
        width: 90%; 
        text-align: center; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    button { 
        padding: 10px 20px; 
        font-size: 16px; 
        cursor: pointer; 
        border-radius: 5px; 
        border: none; 
        background-color: #007BFF; 
        color: white; 
        margin-top: 15px;
        transition: background 0.3s;
    }
    button:hover { background-color: #0056b3; }
    .success { color: green; margin-top: 10px; }
    .error { color: red; margin-top: 10px; }
    .info { margin: 15px 0; font-size: 14px; color: #555; word-break: break-word; }
</style>
</head>
<body>
<h1>Atualização de Data e Hora</h1>

<div class="card">
    <?php if (!empty($dados["siteconfig"]["datetime"])): ?>
        <div class="info">
            <strong>Última Data Atualizada:</strong><br>
            <?php echo formatarData($dados['siteconfig']['datetime']); ?><br>
            <strong>Servidor Utilizado:</strong><br>
            <?php echo $dados['siteconfig']['lastclockserver'] ?: '-'; ?><br>
            <strong>Arquivo gravado:</strong><br>
            <?php echo $configFile; ?>
        </div>
    <?php else: ?>
        <p><strong>Última atualização:</strong> Ainda não houve atualização.</p>
    <?php endif; ?>

    <?php if (!empty($mensagem)): ?>
        <p class="<?php echo strpos($mensagem, '❌') !== false ? 'error' : 'success'; ?>"><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="atualizar">Atualizar data e hora</button>
    </form>
</div>

</body>
</html>
