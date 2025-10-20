<?php
// Arquivo: webjornal.php
ini_set('default_charset', 'UTF-8');
$configFile = "config.json";

// Função para ler datetime do config.json
function obterDataConfig($file) {
    if (!file_exists($file)) return null;
    $json = file_get_contents($file);
    $dados = json_decode($json, true);
    if (!$dados || empty($dados["siteconfig"]["datetime"])) return null;
    return $dados["siteconfig"]["datetime"];
}

// Função para formatar datetime YYYYmmddThhmmss para formato extenso em português
function formatarDataExtenso($datetimeStr) {
    if (!$datetimeStr) return "Data não disponível";
    $dt = DateTime::createFromFormat('Ymd\THis', $datetimeStr);
    if (!$dt) return "Data inválida";

        setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR.UTF-8', 'portuguese');
        $dataFormatada = strftime('%A, %d de %B de %Y', $dt->getTimestamp());
        
        // Usa mbstring se disponível
        if (function_exists('mb_convert_case')) {
            $dataFormatada = mb_convert_case($dataFormatada, MB_CASE_TITLE, "UTF-8");
            $dataFormatada = str_replace(["De ", "Feira"], ["de ", "feira"], $dataFormatada);
        } else {
            $dataFormatada = ucwords($dataFormatada);
        }

    // Verifica se a função mb_convert_case existe antes de usar
    if (function_exists('mb_convert_case')) {
        $dataFormatada = mb_convert_case($dataFormatada, MB_CASE_TITLE, "UTF-8");
        $dataFormatada = str_replace(["De ", "Feira"], ["de ", "feira"], $dataFormatada);
    } else {
        // Caso mbstring não esteja habilitada, usa fallback simples
        $dataFormatada = ucwords($dataFormatada);
    }

    return $dataFormatada;
}

$datetimeConfig = obterDataConfig($configFile);
$dataExtenso = formatarDataExtenso($datetimeConfig);
?>

<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="description" content="Jornal Gazeta de Notícias - Atualizações e reportagens">
<meta name="author" content="Fábio de Almeida Martins">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
<title>Gazeta de Notícias - Página Inicial</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="./css/jornalweb.css">
<script src="./js/JQuery3-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<main class="container-main">
    <header class="text-center">
        <h1 id="tituloprincipal">Gazeta de Notícias</h1>
        <h4 class="slogan">O que você precisa SABER está aqui</h4>
        <h6 class="cabecalho"><?= $dataExtenso ?>. Ano I. N. 1.</h6>
        <hr>
    </header>

    <div id="container-colunas" class="container-colunas"></div>

    <footer>
        <p><small><b>Gazeta de Notícias</b> — Desenvolvido por Fábio de Almeida Martins<br>© 2025 - Direitos Reservados</small></p>
    </footer>
</main>

<script type="text/javascript" src="./js/jornalweb.js"></script>

</body>
</html>
