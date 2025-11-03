<?php
ini_set('default_charset', 'UTF-8');
$configFile = "config.json";

function obterDadosConfig($file, $campo) {
    if (!file_exists($file)) return null;
    $json = file_get_contents($file);
    $dados = json_decode($json, true);
    return $dados["siteconfig"][$campo] ?? null;
}

function calculaNebulosidade($nebul, $pluv, $horario) {
    if (intval($horario) < 600 || intval($horario) >= 1800) {
        //noite
        if ($pluv != 0) {
            if ($nebul > 50) {
                return "chuva à noite";
            } else if ($nebul > 10) {
                return "chuvas esparsas à noite";
            } else if ($nebul <= 10) {
                return "algumas nuvens à noite";
            } else if ($nebul == 0) {
                return "céu limpo à noite";
            } else {
                return "erro de nebulosidade";
            }
        } else if ($pluv == 0) {
            if ($nebul > 90) {
                return "nublado à noite";
            } else if ($nebul > 50) {
                return "parcialmente nublado à noite";
            } else if ($nebul > 10) {
                return "algumas nuvens à noite";
            } else if ($nebul <= 10) {
                return "céu limpo à noite";
            } else {
                return "erro de nebulosidade";
            }
        } else return "";
    } else if (intval($horario) > 600 && intval($horario) < 1800) {
        //dia
        if ($pluv == 0) {
            if ($nebul > 90) {
                return "céu nublado";
            } else if ($nebul > 70) {
                return "céu parcialmente nublado";
            } else if ($nebul > 50) {
                return "sol entre nuvens";
            } else if ($nebul > 30) {
                return "sol com algumas nuvens";
            } else if ($nebul > 10) {
                return "dia parcialmente ensolarado";
            } else if ($nebul <= 10) {
                return "dia ensolarado";
            } else {
                return "erro de nebulosidade";
            }
        } else if ($pluv > 0) {
            if ($nebul >= 50) {
                return "chuvoso";
            } else if ($nebul < 50) {
                return "sol e chuvas esparsas";
            } else {
                return "erro de nebulosidade"; 
            }
        } else return "erro de pluviosidade.";
    }
}

function formatarDataExtenso($datetimeStr) {
    if (!$datetimeStr) return "Data não disponível";
    $dt = DateTime::createFromFormat('Ymd\THis', $datetimeStr);
    if (!$dt) return "Data inválida";

    setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR.UTF-8', 'portuguese');
    $dataFormatada = strftime('%A, %d de %B de %Y', $dt->getTimestamp());

    $diasCorrigidos = [
        'segunda' => 'Segunda-feira',
        'terça' => 'Terça-feira',
        'terca' => 'Terça-feira',
        'quarta' => 'Quarta-feira',
        'quinta' => 'Quinta-feira',
        'sexta' => 'Sexta-feira'
    ];

    foreach ($diasCorrigidos as $semFeira => $comFeira) {
        if (stripos($dataFormatada, $semFeira) === 0) {
            if (stripos($dataFormatada, '-feira') === false) {
                $dataFormatada = $comFeira . substr($dataFormatada, strlen($semFeira));
            }
            break;
        }
    }

    if (function_exists('mb_convert_case')) {
        $dataFormatada = mb_convert_case($dataFormatada, MB_CASE_LOWER, "UTF-8");
        $dataFormatada = ucfirst($dataFormatada);
    } else {
        $dataFormatada = ucwords($dataFormatada);
    }

    return $dataFormatada;
}

$colunas = obterDadosConfig($configFile, 'colunas') ?? 4; 
$datetimeConfig = obterDadosConfig($configFile, 'datetime');
$dataExtenso = formatarDataExtenso($datetimeConfig);
$temperaturaSp = obterDadosConfig($configFile, 'temperaturaatual') ?? null;
$pluviosidadeSp = obterDadosConfig($configFile, 'chuvaatual') ?? null;
$horarioclimaSp = substr(obterDadosConfig($configFile, 'atualizaclima'), 9, 4);
$nebulosidadeSp = calculaNebulosidade(obterDadosConfig($configFile, 'nebulosidadeatual'), $pluviosidadeSp, $horarioclimaSp);
$titulo = obterDadosConfig($configFile, 'sitename');
$slogan = obterDadosConfig($configFile, 'siteslogan');
$owner = obterDadosConfig($configFile, 'siteowner');
$developer = obterDadosConfig($configFile, 'sitedeveloper');
$direitos = obterDadosConfig($configFile, 'sitecopyright');

// cria a variável $anonumero
$anonumero = "";
if (obterDadosConfig($configFile, 'ano')) $anonumero = "Ano " . obterDadosConfig($configFile, 'ano');
if (obterDadosConfig($configFile, 'numero')) {
    if (!empty($anonumero)) $anonumero .= ' - ';
    $anonumero .= "Nº " . obterDadosConfig($configFile, 'numero');
}
if (obterDadosConfig($configFile, 'edicao')) {
    if (!empty($anonumero)) $anonumero .= ' - ';
    $anonumero .= "Edição n. " . obterDadosConfig($configFile, 'edicao');
}
if (!empty($anonumero)) $anonumero .= ". ";
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
    <style> /* --- CSS incorporado (jornalweb.css) --- */
/* ===========================
   VARIÁVEIS GLOBAIS
   =========================== */
:root {
    --gap: 2%;
}

/* ===========================
   RESET E BODY
   =========================== */
*, html, main { margin:0; padding:0; box-sizing:border-box; }
body {
    margin: 0 5%;
    font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
    background-color: #F5F5F5;
    color: #222;
    line-height: 1;
    font-family: Georgia, 'Times New Roman', serif;
}
h1,h2,h3,h4,h5,h6 { margin:0; }
a, a:hover { color: #000; text-decoration:none; }
a.link {
    color: #00D;
    text-decoration: none; 
    font-weight: bold;
}
a.hover:hover { 
    text-decoration:underline; 
    font-weight:bold; 
}
main { width:100%; }
p {margin: 0;}

/* ===========================
   FONTES TIPOGRÁFICAS EXTERNAS
   =========================== */
@font-face {
  font-family: "Led Counter"; /* Nome que você usará no CSS */
  src: url("./fonts/led_counter-7.ttf") format("ttf");
  font-weight: normal;
  font-style: normal;
}

.led-counter {
    font-family: 'LED Counter 7', monospace;
}

/* ===========================
   CABEÇALHO
   =========================== */
h1#tituloprincipal {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 4.5em;
    margin:5px 0;
    line-height: 1;
}
h4.slogan {
    margin-bottom:20px;
    line-height: 1;
}
.cabecalho {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
    margin-top: 10px;
    font-weight: normal;
    line-height: 1;
    font-size: 0.95em;
    width: 100%;
}
.cabecalho-item { flex: 1; }
.cab-left { text-align: left; }
.cab-right { text-align: right; }
.cab-center {
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
    box-sizing: border-box;
    /* background-color: #333;
    color: #FF6;
    font-family: 'LED Counter 7', monospace;
    font-size: 20px; */
}
.cab-center span.letreiro {
    display: inline-block;
    padding-left: 100%;
    animation: scroll-letreiro 20s linear infinite;
}
@keyframes scroll-letreiro {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

/* ===========================
   CONTAINER DE CARDS
   =========================== */
.container-colunas { 
    display: flex; 
    flex-direction: column; 
    gap: var(--gap); 
    width: 100%; 
    margin: 0; 
    padding: 0; 
    font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
}
.linha-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    align-items: stretch;
    width: 100%;
    gap: var(--gap);
    margin: calc(var(--gap)/2) 0;
}
.container-colunas > .linha-cards:last-child {
    margin-bottom: 0;
}

/* ===========================
   CARD
   =========================== */
.card {
    display: flex;
    flex-direction: column; /* garante que os elementos fiquem empilhados */
    background-color: inherit !important; /* #FFF */
    border-radius: 5px;
    /*box-shadow: 0 2px 5px rgba(0,0,0,0.1); */
    overflow: hidden;
    padding: 5px;
    border: none;
    width: 100%;
}
.card[data-padding] { padding: attr(data-padding px, 5px); }
@supports not (padding: attr(data-padding px, 5px)) {
    .card { padding: 5px; }
}
.card-chapeu {
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 8px;
    padding: 2px 0;
}

/* Imagem */
.card-imagem {
    width: 100%;
    text-align: center;
    margin: 0 0 15px;
}
.card-imagem img {
    width: 100%;
    border-radius: 6px;
    object-fit: cover; /* garante preenchimento sem distorcer */
    aspect-ratio: 4/3;
}
.img-legenda {
    font-size: 0.75em;
    color: #555;
    margin-top: 4px;
    text-align: center;
}

/* Corpo horizontal (usado para img à esquerda/direita) */
.card-corpo {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 15px;
}
/* bloco imagem lateral (35%) */
.card-imagem-lateral {
    flex: 0 0 35%;
    max-width: 35%;
    text-align: left;
}
.card-imagem-lateral img {
    width: 100%;
    height: auto;
    border-radius: 6px;
    object-fit: cover;
    display: block;
    aspect-ratio: 4/3;
}

/* BLOCO DIREITO (TEXTO) */
.card-texto {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    text-align: left;
}
.card-title {
    font-size: 1.1em;
    font-weight: bold;
    margin: 0 0 10px;
    line-height: 1.2;
    text-align: start;
}
.card-title a {
    color: #111;
    text-decoration: none;
}
.card-title a:hover { text-decoration: underline; }
.card-data {
    font-size: 0.75em;
    color: #666;
}
.card-content {
    flex-grow: 1;
    margin-bottom: 10px;
    font-family:'Georgia','Times New Roman','serif'; 
    line-height: 1.8;
    text-align: justify !important;
}
.card-content p {
    margin: 15px 0;
    color: #222;
    word-wrap: normal;
    overflow-wrap: normal;
    /* Habilita a hifenização automática */
    hyphens: auto;
    -webkit-hyphens: auto;
    -moz-hyphenz: auto;
    -ms-hyphens: auto;
}
.hyphenate {
    word-wrap: break-word;
    overflow-wrap: break-word;
    /* Habilita a hifenização automática */
    hyphens: auto;
    -webkit-hyphens: auto;
    -moz-hyphenz: auto;
    -ms-hyphens: auto;
}
.card-content a.link {
    color: #0077cc;
    text-decoration: none;
}
.card-content a.link:hover { text-decoration: underline; }

/* RESPONSIVIDADE */
@media (max-width: 768px) {
    .card-corpo { flex-direction: column; }
    .card-imagem { max-width: 100%; flex: none; }
}

/* FOOTER */
footer {
    text-align:center;
    margin-top:40px;
    padding:10px 0;
    border-top:1px solid #ccc;
    font-size:0.85em;
    color: #555;
    font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
}

/* CARDS AUXILIARES (SKELETON) */
.skeleton {
    border: none;
    padding:5px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    background:#ddd;
    opacity: 0.5;
    border-radius:5px;
    min-height:200px;
    flex:1;
    animation:skeleton-loading 0.8s linear infinite alternate;
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
    <header class="">
        <h1 id="tituloprincipal"><a href=""> <?= ucfirst(htmlspecialchars($titulo)) ?> </a></h1>
        <h4 class="slogan"><?= htmlspecialchars($slogan) ?></h4>
        <div class="cabecalho">
            <div class="cabecalho-item cab-left">
                <?= htmlspecialchars($dataExtenso) ?>.
            </div>
            <div class="cabecalho-item cab-center">
                <span class="letreiro"><?php if ($temperaturaSp): ?>Previsão do tempo em São Paulo (SP): <?php if (!empty($nebulosidadeSp)) echo htmlspecialchars(ucfirst($nebulosidadeSp)) . ', '; ?><?= htmlspecialchars($temperaturaSp) ?> °C às <?= htmlspecialchars(substr($horarioclimaSp, 0, 2) . "h" . substr($horarioclimaSp, 2, 2)) ?>. <?php endif; ?> </span>
            </div>
            <div class="cabecalho-item cab-right">
                <?= htmlspecialchars($anonumero) ?>
            </div>
        </div>
        <hr>
    </header>
    <div id="container-conteudo-01" class="container-colunas"></div>
    <footer>
        <p style="line-height: 1;"><small><b><?= htmlspecialchars($titulo) ?></b> — Desenvolvido por <?= htmlspecialchars($developer) ?><br><?= htmlspecialchars($direitos) ?></small></p>
    </footer>
</main>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    // --- Lê configurações diretamente do config.json ---
    async function carregarConfig() {
        try {
            const resp = await fetch("config.json");
            if (!resp.ok) throw new Error("Erro ao carregar config.json");
            const json = await resp.json();
            return json.siteconfig || {};
        } catch (e) {
            console.error("Falha ao carregar configurações:", e);
            return {};
        }
    }

    const config = await carregarConfig();
    const numColunas = parseInt(config.colunas || 4);
    const posicaoImagem = config.posicaoimagem || "sup";
    const cardPadding = config.cardoadding || "10px";
    const gapPercent = parseFloat(config.gappercent || 2); // ← lê o valor do JSON
    const cardWidth = (100 - (numColunas - 1) * gapPercent) / numColunas;

    // Função para criar cada item card, agora recebe imgpos
    function criarCard(item, imgpos = "sup") {
        if (!item || !item.title) return null;

        const card = document.createElement("div");
        card.classList.add("card");
        // aplica padding configurável
        card.style.padding = cardPadding;

        // Chapéu
        const chapeuHTML = item.chapeu
            ? `<div class="card-chapeu">${item.chapeu}</div>`
            : "";

        // HTML imagem (vertical)
        let imagemHTML = "";
        if (item.image) {
            imagemHTML = `
            <div class="card-imagem">
                <a href="${item.url || '#'}" target="_blank" rel="noopener noreferrer">
                    <img src="${item.image}" alt="${item.title}" title="${item.title}" loading="lazy">
                </a>
                ${item.imgdescript || item.imgrights ? `
                <p class="img-legenda">
                    ${item.imgdescript ? `${item.imgdescript}. ` : ""}
                    ${item.imgrights ? `${item.imgrights}` : ""}
                </p>` : ""}
            </div>`;
        }

        // HTML imagem lateral (usado em esq/dir)
        let imagemLateralHTML = "";
        if (item.image) {
            imagemLateralHTML = `
            <div class="card-imagem-lateral">
                <a href="${item.url || '#'}" target="_blank" rel="noopener noreferrer">
                    <img src="${item.image}" alt="${item.title}" title="${item.title}" loading="lazy">
                </a>
                ${item.imgdescript || item.imgrights ? `
                <p class="img-legenda">
                    ${item.imgdescript ? `${item.imgdescript}. ` : ""}
                    ${item.imgrights ? `${item.imgrights}` : ""}
                </p>` : ""}
            </div>`;
        }

        // Texto (com título, data, conteúdo)
        const dataPubl = item.datetime
            ? new Date(item.datetime).toLocaleDateString("pt-BR")
            : "";

        const textoHTML = `
            <div class="card-texto">
                <div class="card-title">
                    <a href="${item.url || '#'}" target="_blank" rel="noopener noreferrer">
                        ${item.title}
                    </a>
                </div>
                ${dataPubl ? `<p class="card-data">${dataPubl}</p>` : ""}
                <div class="card-content">
                    ${item.location ? `<b>${item.location.toUpperCase()} - </b>` : ""}
                    ${item.content || ""}
                    ${item.url ? ` <a href="${item.url}" class="link" target="_blank" rel="noopener noreferrer">Leia mais</a>` : ""}
                </div>
            </div>`;

        // Montagem dependendo de imgpos
        if (imgpos === "esq" || imgpos === "dir") {
            // chapéu no topo + corpo horizontal
            const corpo = document.createElement("div");
            corpo.classList.add("card-corpo");

            if (imgpos === "esq") {
                // imagem lateral à esquerda
                corpo.innerHTML = `${imagemLateralHTML}${textoHTML}`;
            } else {
                // imagem lateral à direita
                corpo.innerHTML = `${textoHTML}${imagemLateralHTML}`;
            }

            card.innerHTML = `${chapeuHTML}`;
            card.appendChild(corpo);
        } else {
            // comportamento vertical (sup = imagem entre chapeu e título)
            card.innerHTML = `
                ${chapeuHTML}
                ${imagemHTML}
                ${textoHTML}
            `;
        }

        return card;
    }

    // Função para ler o arquivo JSON de conteúdo
    async function extrairConteudo(arquivo, campo = null, valor = null) {
        try {
            const responseJson = await fetch(arquivo);
            if (!responseJson.ok) throw new Error("Falha ao carregar conteúdo JSON.");

            const data = await responseJson.json();
            let conteudoArquivo = Object.values(data.conteudo || {}).filter(
                i =>
                    i &&
                    i.type === "reportagem" &&
                    (i.hidden === 0 || i.hidden === false || i.hidden === null || i.hidden === undefined) &&
                    (i.archive === 0 || i.archive === false || i.archive === null || i.archive === undefined)
            );

            if (campo && valor !== null) {
                const vLower = (typeof valor === "string") ? valor.toLowerCase() : valor;
                conteudoArquivo = conteudoArquivo.filter(item => {
                    if (item[campo] === undefined || item[campo] === null) return false;
                    if (typeof item[campo] === "string") return item[campo].toLowerCase() === vLower;
                    return item[campo] == valor;
                });
            }

            return conteudoArquivo;
        } catch (err) {
            console.error("Erro ao extrair conteúdo:", err);
            return [];
        }
    }

    // Monta as linhas e cards dinamicamente
    // assinado como solicitado: imgpos="sup" por padrão
    async function preencherConteudo(containerId, arquivo, imgpos = "sup", campo = null, valor = null) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container "${containerId}" não encontrado.`);
            document.body.innerHTML += `<p><em>Erro ao carregar o conteúdo.</em></p>`;
            return;
        }

        // --- Skeletons de carregamento ---
        container.innerHTML = "";

        let linhaSkeleton = document.createElement("div");
        linhaSkeleton.classList.add("linha-cards");
        linhaSkeleton.style.display = "flex";
        linhaSkeleton.style.gap = `${gapPercent}%`;
        linhaSkeleton.style.margin = `${gapPercent / 2}% 0`;
        linhaSkeleton.style.flexWrap = "wrap";

        for (let i = 0; i < numColunas * 2; i++) {
            const skeleton = document.createElement("div");
            skeleton.classList.add("skeleton");
            skeleton.style.flex = `0 0 ${cardWidth}%`;
            skeleton.style.padding = cardPadding;
            linhaSkeleton.appendChild(skeleton);

            if ((i + 1) % numColunas === 0) {
                container.appendChild(linhaSkeleton);
                linhaSkeleton = document.createElement("div");
                linhaSkeleton.classList.add("linha-cards");
                linhaSkeleton.style.display = "flex";
                linhaSkeleton.style.gap = `${gapPercent}%`;
                linhaSkeleton.style.margin = `${gapPercent / 2}% 0`;
                linhaSkeleton.style.flexWrap = "wrap";
            }
        }
        if (linhaSkeleton.childNodes.length > 0) {
            container.appendChild(linhaSkeleton);
        }

        try {
            const conteudoJSON = await extrairConteudo(arquivo, campo, valor);
            container.innerHTML = "";

            if (!conteudoJSON.length) {
                container.innerHTML = "<p>Nenhum conteúdo disponível.</p>";
                return;
            }

            const fragment = document.createDocumentFragment();
            let linha = document.createElement("div");
            linha.classList.add("linha-cards");
            linha.style.display = "flex";
            linha.style.flexWrap = "wrap";
            linha.style.justifyContent = "flex-start";
            linha.style.gap = `${gapPercent}%`;
            linha.style.margin = `${gapPercent / 2}% 0`;

            let countVisible = 0;

            conteudoJSON.forEach(item => {
                const card = criarCard(item, imgpos);
                if (!card) return;

                // Largura do card com base no número de colunas
                card.style.flex = `0 0 ${cardWidth}%`;
                card.style.marginBottom = "0"; // margem inferior entre linhas

                linha.appendChild(card);
                countVisible++;

                // Ao completar uma linha, cria nova linha
                if (countVisible % numColunas === 0) {
                    fragment.appendChild(linha);
                    linha = document.createElement("div");
                    linha.classList.add("linha-cards");
                    linha.style.display = "flex";
                    linha.style.flexWrap = "wrap";
                    linha.style.justifyContent = "flex-start";
                    linha.style.gap = `${gapPercent}%`;
                    linha.style.margin = `${gapPercent / 2}% 0`;
                }
            });

            if (linha.childNodes.length > 0) fragment.appendChild(linha);
            container.appendChild(fragment);

        } catch (err) {
            console.error(err);
            container.innerHTML = "<em>Erro ao carregar o conteúdo.</em>";
        }
    }

    // Se quiser outro layout, troque "sup" (superior) por "esq" (esquerda) ou "dir" (direita)
    preencherConteudo("container-conteudo-01", "conteudo.json", posicaoImagem, "type", "reportagem");
});
</script>

</body>
</html>
