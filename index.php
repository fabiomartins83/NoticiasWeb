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

    try {
        // Usando IntlDateFormatter em vez de strftime
        $fmt = new IntlDateFormatter(
            'pt_BR',                     // locale
            IntlDateFormatter::FULL,      // formato de data completo
            IntlDateFormatter::NONE,      // sem hora
            $dt->getTimezone(),
            IntlDateFormatter::GREGORIAN
        );

        $dataFormatada = $fmt->format($dt);
    } catch (\Exception $e) {
        // Fallback caso IntlDateFormatter não funcione
        setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR.UTF-8', 'portuguese');
        $dataFormatada = strftime('%A, %d de %B de %Y', $dt->getTimestamp());
        
        // Usa mbstring se disponível
        if (function_exists('mb_convert_case')) {
            $dataFormatada = mb_convert_case($dataFormatada, MB_CASE_TITLE, "UTF-8");
            $dataFormatada = str_replace(["De ", "Feira"], ["de ", "feira"], $dataFormatada);
        } else {
            $dataFormatada = ucwords($dataFormatada);
        }
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
<script src="./js/JQuery3-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
*, html, main { margin:0; padding:0; box-sizing:border-box; }

body {
    margin: 0 5%;
    font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
    background-color:#EEE;
    color:#000;
    line-height:1.4;
}

h1,h2,h3,h4,h5,h6 { margin:0; }
a { color: #000; text-decoration:none; }
a:hover {color: inherit;}
a.link {color: #00F; text-decoration: none; font-weight: bold;}
a.link:hover { color: #00F; text-decoration:underline; font-weight:bold; }

main { width:100%; }

h1#tituloprincipal {
    font-family: Garamond, 'Times New Roman', serif;
    font-size: 4.5em;
    text-align:center;
    margin:20px 0;
    line-height: 1;
}
h4.slogan {
    text-align:center;
    font-weight:normal;
    margin-bottom:10px;
    line-height: 1;
}
h6.cabecalho {
    text-align: right;
    font-weight:normal;
    line-height: 1;
}
hr { line-height: 1; }

.container-colunas {
    display:flex;
    flex-wrap:wrap;
    gap:2%;
    margin:1%;
    justify-content:flex-start;
}
.linha-cards {
    display:flex;
    width:100%;
    gap:2%;
    margin:1%;
    flex-wrap:nowrap;
}

.card {
    flex:1;
    background:#FFF;
    border-radius:5px;
    border: 1px #000;
    padding:5px;
    min-height:200px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.card-img {
    width:100%;
    border-radius:5px;
    object-fit:cover;
    aspect-ratio:4/3;
}

.card-title {
    font-weight:bold;
    text-align:center;
    margin:8px 0;
    word-break: break-word;
    text-decoration:none !important;
    color:#000 !important;
}

.card-content {
    flex-grow:1;
    font-size:0.9em;
    text-align:justify;
    margin-bottom:10px;
    font-family:'Georgia','Times New Roman','serif';
}

.img-legenda, .img-descricao {
    font-size:0.8em;
    text-align:right;
    color:inherit;
    margin:4px 0;
}

footer {
    text-align:center;
    margin-top:40px;
    padding:10px 0;
    border-top:1px solid #ccc;
    font-size:0.85em;
    color:#333;
}

.skeleton {
    background:#ddd;
    border-radius:15px;
    min-height:200px;
    flex:1;
    animation:skeleton-loading 1.2s linear infinite alternate;
}
@keyframes skeleton-loading {
    0% { background-color:#ddd; }
    50% { background-color:#ccc; }
    100% { background-color:#ddd; }
}
</style>
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

<script>
document.addEventListener("DOMContentLoaded", async () => {
    const container = document.getElementById("container-colunas");
    const numColunas = 4;
    const gapPercent = 2;
    const cardWidth = (100 - (numColunas - 1) * gapPercent) / numColunas;
    let conteudoJSON = [];

    // Skeleton temporário
    for (let i = 0; i < numColunas * 2; i++) {
        const s = document.createElement("div");
        s.classList.add("skeleton");
        s.style.flex = `0 0 ${cardWidth}%`;
        container.appendChild(s);
    }

    function criarCard(item) {
        if (!item || !item.title) return null;

        const card = document.createElement("div");
        card.classList.add("card");

        let imgHTML = "";
        if (item.image) {
            imgHTML = `
                <div class="img-container">
                    <a href="${item.url || '#'}" target="_blank" rel="noopener noreferrer">
                        <img src="${item.image}" class="card-img" loading="lazy" alt="${item.title}">
                    </a>
                    ${item.imgrights ? `<p class="img-legenda">${item.imgrights}</p>` : ""}
                </div>`;
        } else if (item.imgdescript) {
            imgHTML = `<p class="img-descricao"><em>${item.imgdescript}</em></p>`;
        }

        const dataPubl = item.datetime ? new Date(item.datetime).toLocaleDateString("pt-BR") : "";

        card.innerHTML = `
            ${item.chapeu ? `<div class="direita" style="font-size:0.8em;font-weight:bold;text-transform:uppercase;margin-bottom:4px;">${item.chapeu}</div>` : ""}
            ${imgHTML}
            <div class="card-title">
                <a href="${item.url || '#'}" class="" target="_blank" rel="noopener noreferrer">${item.title}</a>
            </div>
            <p class="direita" style="font-size:0.75em;">${dataPubl}</p>
            <div class="card-content">
                <p>${item.location ? `<b>${item.location.toUpperCase()} - </b>` : ""}${item.content || ""} […] 
                ${item.url ? `<a href="${item.url}" class="link" target="_blank" rel="noopener noreferrer">Leia mais</a>` : ""}</p>
            </div>`;
        card.style.flex = `0 0 ${cardWidth}%`;
        return card;
    }

    try {
        const response = await fetch("conteudo.json");
        if (!response.ok) throw new Error("Falha ao carregar conteúdo JSON.");

        const data = await response.json();
        container.innerHTML = "";

        conteudoJSON = Object.values(data.conteudo || {}).filter(i => i && i.type === "reportagem");
        if (conteudoJSON.length === 0) {
            container.innerHTML = "<em>Nenhuma reportagem encontrada.</em>";
            return;
        }

        const fragment = document.createDocumentFragment();
        let linha = document.createElement("div");
        linha.classList.add("linha-cards");

        conteudoJSON.forEach((item, i) => {
            const card = criarCard(item);
            if (card) linha.appendChild(card);
            if ((i + 1) % numColunas === 0) {
                fragment.appendChild(linha);
                linha = document.createElement("div");
                linha.classList.add("linha-cards");
            }
        });
        if (linha.childNodes.length > 0) fragment.appendChild(linha);
        container.appendChild(fragment);

    } catch (err) {
        console.error(err);
        container.innerHTML = "<em>Erro ao carregar o conteúdo.</em>";
    }
});
</script>
</body>
</html>
