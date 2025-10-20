<?php
// Arquivo: exibir_data.php

$configFile = "config.json";

// Função para ler datetime do config.json
function obterDataConfig($file) {
    if (!file_exists($file)) return null;
    $json = file_get_contents($file);
    $dados = json_decode($json, true);
    if (!$dados || empty($dados["siteconfig"]["datetime"])) return null;
    return $dados["siteconfig"]["datetime"];
}

// Função para formatar datetime YYYYmmddThhmmss para formato extenso
function formatarDataExtenso($datetimeStr) {
    if (!$datetimeStr) return "Data não disponível";

    $dt = DateTime::createFromFormat('Ymd\THis', $datetimeStr);
    if (!$dt) return "Data inválida";

    // Configura localidade para português do Brasil
    setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'portuguese');

    // Retorna data no formato: Terça-feira, 26 de agosto de 2025
    return strftime('%A, %d de %B de %Y', $dt->getTimestamp());
}

$datetimeConfig = obterDataConfig($configFile);
$dataExtenso = formatarDataExtenso($datetimeConfig);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Data Extensa</title>
<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #f9f9f9;
        color: #333;
        flex-direction: column;
    }
    .card {
        background: #fff;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
    }
    h1 { margin-bottom: 20px; font-size: 24px; }
    p { font-size: 20px; color: #555; }
</style>
</head>
<body>
<div class="card">
    <h1>Data Atual do Site</h1>
    <p><?php echo $dataExtenso; ?></p>
</div>
</body>
</html>
