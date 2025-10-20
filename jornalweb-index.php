<?php
// index.php
ini_set('default_charset', 'UTF-8');
$configFile = "config.json";

// Função para ler datetime do config.json
function obterDataConfig($file) {
    if (!file_exists($file)) return null;
    $json = file_get_contents($file);
    $dados = json_decode($json, true);
    return $dados["siteconfig"]["datetime"] ?? null;
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

    return $dataFormatada;
}

$datetimeConfig = obterDataConfig($configFile);
$dataExtenso = formatarDataExtenso($datetimeConfig);

// Lê o HTML do template
$template = file_get_contents('jornalweb-index.html');
// Substitui o placeholder pelo valor real
$template = str_replace('{{DATA_EXTENSO}}', htmlspecialchars($dataExtenso), $template);

// Exibe o HTML final
echo $template;
?>
