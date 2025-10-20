<?php
$configFile = "config.json";

$clockServers = [
    "https://worldtimeapi.org/api/timezone/America/Sao_Paulo",
    "https://timeapi.io/api/Time/current/zone?timeZone=America/Sao_Paulo",
    "https://worldclockapi.com/api/json/br/now"
];

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

function obterDataHora($url) {
    $response = @file_get_contents($url);
    if ($response !== false) {
        $dadosServ = json_decode($response, true);
        if ($dadosServ) {
            if (isset($dadosServ['datetime'])) {
                $datetime = substr($dadosServ['datetime'], 0, 19);
                return [str_replace(['-', ':'], '', $datetime), $url];
            }
            if (isset($dadosServ['dateTime'])) {
                $datetime = substr($dadosServ['dateTime'], 0, 19);
                return [str_replace(['-', ':'], '', $datetime), $url];
            }
            if (isset($dadosServ['currentDateTime'])) {
                $datetime = substr($dadosServ['currentDateTime'], 0, 19);
                return [str_replace(['-', ':'], '', $datetime), $url];
            }
        }
    }
    return [null, null];
}

if (file_exists($configFile)) {
    $dados = json_decode(file_get_contents($configFile), true);
    if (!$dados) $dados = $defaultConfig;
} else {
    $dados = $defaultConfig;
}

if (isset($_POST["atualizar"])) {
    foreach ($clockServers as $server) {
        list($datetime, $usedServer) = obterDataHora($server);
        if ($datetime) {
            $dtObj = new DateTime($datetime);
            $formatted = $dtObj->format('Ymd\THis');

            $dados["siteconfig"]["datetime"] = $formatted;
            $dados["siteconfig"]["lastclockserver"] = $usedServer;

            file_put_contents($configFile, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            break;
        }
    }
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
            height: 100vh;
            margin: 20px;
        }
        h1 { margin-bottom: 20px; }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            max-width: 400px;
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
    </style>
</head>
<body>
    <h1>Atualização de Data e Hora</h1>

    <div class="card">
        <?php if (!empty($dados["siteconfig"]["datetime"])): ?>
            <p><strong>Última Data Atualizada:</strong><br><?php echo $dados["siteconfig"]["datetime"]; ?></p>
            <p><strong>Servidor Utilizado:</strong><br><?php echo $dados["siteconfig"]["lastclockserver"]; ?></p>
            <p><strong>Arquivo gravado:</strong><br><?php echo $configFile; ?></p>
        <?php else: ?>
            <p><strong>Última atualização:</strong> Ainda não houve atualização.</p>
        <?php endif; ?>
    </div>

    <form method="post">
        <button type="submit" name="atualizar">Atualizar data e hora</button>
    </form>
</body>
</html>
