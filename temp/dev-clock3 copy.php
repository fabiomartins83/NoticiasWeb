<?php
// Nome do arquivo de configuração
$configFile = "config.json";

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

// Função para obter data/hora de um servidor
function obterDataHora() {
    $servers = [
        "https://worldtimeapi.org/api/timezone/America/Sao_Paulo",
        "https://timeapi.io/api/Time/current/zone?timeZone=America/Sao_Paulo"
    ];

    foreach ($servers as $server) {
        $response = @file_get_contents($server);
        if ($response !== false) {
            $data = json_decode($response, true);

            if (isset($data['datetime'])) {
                $datetime = str_replace(['-', ':'], '', substr($data['datetime'], 0, 19));
                $datetime = str_replace('T', 'T', $datetime);
                return [$datetime, $server];
            }

            if (isset($data['dateTime'])) {
                $datetime = str_replace(['-', ':'], '', substr($data['dateTime'], 0, 19));
                $datetime = str_replace('T', 'T', $datetime);
                return [$datetime, $server];
            }
        }
    }
    return [null, null];
}

// Carregar ou inicializar config.json
if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
    if (!$config) $config = $defaultConfig;
} else {
    $config = $defaultConfig;
}

// Se o botão for clicado → atualizar datetime e lastclockserver
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["atualizar"])) {
    list($datetime, $server) = obterDataHora();
    if ($datetime && $server) {
        $config["siteconfig"]["datetime"] = $datetime;
        $config["siteconfig"]["lastclockserver"] = $server;
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
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
        .success { color: green; margin-top: 10px; }
        .error { color: red; margin-top: 10px; }
        .info { margin: 15px 0; font-size: 14px; color: #555; word-break: break-word; }
    </style>
</head>
<body>
    <h1>Atualização de Data e Hora</h1>

    <div class="info">
        <?php if (!empty($config["siteconfig"]["datetime"])): ?>
            <p><strong>Última atualização:</strong> <?= htmlspecialchars($config["siteconfig"]["datetime"]) ?></p>
            <p><strong>Servidor:</strong> <?= htmlspecialchars($config["siteconfig"]["lastclockserver"]) ?></p>
            <p><strong>Arquivo:</strong> <?= $configFile ?></p>
        <?php else: ?>
            <p><strong>Última atualização:</strong> Ainda não houve atualização.</p>
        <?php endif; ?>
    </div>

    <form method="post">
        <button type="submit" name="atualizar">Atualizar data e hora</button>
    </form>
</body>
</html>
