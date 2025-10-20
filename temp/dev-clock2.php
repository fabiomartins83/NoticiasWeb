<?php

$configFile = 'config.json';
$dados = ["datetime" => "", "lastclockserver" => ""];
$result = ["success" => false, "message" => ""];

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

// Lê arquivo se existir
if (file_exists($configFile)) {
    $json = file_get_contents($configFile);
    $dados = json_decode($json, true);
    if (!$dados) $dados = $defaultConfig;
} else {
    $dados = $defaultConfig;
}

// Função para obter hora atual de endpoint
function getAtomicTime($url) {
    $opts = [
        "http" => [
            "method" => "GET",
            "timeout" => 5
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    if (!$response) return false;

    $data = json_decode($response, true);
    if (!$data) return false;

    if (isset($data['datetime'])) return $data['datetime'];
    if (isset($data['dateTime'])) return $data['dateTime'];
    if (isset($data['currentDateTime'])) return $data['currentDateTime'];

    return false;
}

if (isset($_POST['atualizar'])) {
    $datetime = false;
    $usedServer = '';

    foreach ($clockServers as $server) {
        $dt = getAtomicTime($server);
        if ($dt) {
            $datetime = $dt;
            $usedServer = $server;
            break;
        }
    }

    if ($datetime) {
        $dtObj = new DateTime($datetime);
        $formatted = $dtObj->format('Ymd\THis');

        $dataToWrite = [
            "datetime" => $formatted,
            "lastclockserver" => $usedServer
        ];

        if (file_put_contents($configFile, json_encode($dataToWrite, JSON_PRETTY_PRINT))) {
            $result['success'] = true;
            $result['message'] = "Arquivo atualizado com sucesso!";
            $dados = $dataToWrite;
        } else {
            $result['message'] = "Erro ao gravar o arquivo JSON.";
        }
    } else {
        $result['message'] = "Não foi possível obter a hora dos servidores.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Atualizar Data e Hora</title>
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
            margin: 0; 
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
        .info { margin-top: 15px; font-size: 14px; color: #555; word-break: break-word; }
    </style>
</head>
<body>
    <h1>Atualizar Data e Hora do Servidor</h1>
    <div class="card">
        <form method="post">
            <button type="submit" name="atualizar">Atualizar Agora</button>
        </form>

        <?php if ($result['message'] != ""): ?>
            <div class="<?php echo $result['success'] ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($result['message']); ?>
            </div>
        <?php endif; ?>

        <div class="info">
            <strong>Última Data Atualizada:</strong><br>
            <?php echo $dados['datetime'] ?: 'Nunca'; ?><br>
            <strong>Servidor Utilizado:</strong><br>
            <?php echo $dados['lastclockserver'] ?: '-'; ?>
        </div>
    </div>
</body>
</html>
