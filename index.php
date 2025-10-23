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
                return "chuvoso à noite";
            } else if ($nebul > 10) {
                return "chuvas esparsas à noite";
            } else if ($nebul <= 10) {
                return "algumas nuvens à noite";
            } else if ($nebul == 0) {
                return "céu limpo à noite";
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
                return "";
            }
        } else return "";
        //dia
        if ($pluv == 0) {
            if ($nebul > 90) {
                return "nublado";
            } else if ($nebul > 70) {
                return "parcialmente nublado";
            } else if ($nebul > 50) {
                return "sol entre nuvens";
            } else if ($nebul > 30) {
                return "algumas nuvens";
            } else if ($nebul > 10) {
                return "parcialmente ensolarado";
            } else if ($nebul <= 10) {
                return "ensolarado";
            } else {
                return "";
            }
        } else if ($pluv > 0) {
            if ($nebul >= 50) {
                return "chuvoso";
            } else if ($nebul < 50) {
                return "sol e chuvas esparsas";
            } else {
                return ""; 
            }
        } else return "";
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
$temperatura = obterDadosConfig($configFile, 'temperaturaatual') ?? null;
$pluviosidade = obterDadosConfig($configFile, 'chuvaatual') ?? null;
$horarioclima = substr(obterDadosConfig($configFile, 'atualizaclima'), 9, 4);
$nebulosidade = calculaNebulosidade(obterDadosConfig($configFile, 'nebulosidadeatual'), $pluviosidade, $horarioclima);
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
    *, html, main { margin:0; padding:0; box-sizing:border-box; }
    body {
        margin: 0 5%;
        font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
        background-color: #F5F5F5; /* antigo #EEE */
        color: #222; /* antigo #000 */
        line-height: 1;
    }
    h1,h2,h3,h4,h5,h6 { margin:0; }
    a, a:hover { color: #000; text-decoration:none; }
    a.link {
        color: #00D;
        text-decoration: none; 
        font-weight: bold;}
    a.hover:hover { 
        text-decoration:underline; 
        font-weight:bold; }
    main { width:100%; }
    h1#tituloprincipal {
        font-family: Garamond, 'Times New Roman', serif;
        font-size: 4.5em;
        text-align:center;
        margin:5px 0;
        line-height: 1;
    }
    h4.slogan {
        text-align:center;
        margin-bottom:20px;
        line-height: 1;
    }
    h6.cabecalho {
        text-align: right;
        font-weight:normal;
        line-height: 1;
    }
    .container-colunas {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2%;
        margin: 0;              /* remove deslocamento lateral */
        padding: 0;             /* garante alinhamento */
        width: 100%;
    }

    .linha-cards {
        display: flex;
        flex-wrap: nowrap;
        gap: 2%;
        justify-content: flex-start;
        align-items: stretch;
        width: 100%;
        margin: 0 auto;         /* centraliza se o container tiver max-width */
        padding: 0;             /* zera qualquer recuo */
    }
    .card {
        background-color: #ffffff;
        color: #222222;
        border-radius: 5px;
        margin: 0;
        overflow: hidden;
        width: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        /* box-shadow: 0 2px 10px rgba(0,0,0,0.1); */
    }

    /* padding dinâmico com base em data-padding */
    .card[data-padding] {
        padding: attr(data-padding px, 16px);
    }

    /* Fallback para navegadores que não suportam attr() */
    @supports not (padding: attr(data-padding px, 16px)) {
        .card {
            padding: 16px; /* valor padrão */
        }
    }
/*
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.15);
    }
*/
    .card h2 {
        font-size: 1.2rem;
        margin: 0 0 8px 0;
        color: #111111;
    }

    .card p {
        font-size: 0.95rem;
        line-height: 1.5;
        margin: 0;
        color: #333333;
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
        line-height: 1;
        margin: 12px 0;
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
        line-height: 1.5;
    }
    .img-legenda, .img-descricao {
        font-size:0.75em;
        text-align:right;
        color:inherit;
        margin:0;
        line-height: 1;
    }
    footer {
        text-align:center;
        margin-top:40px;
        padding:10px 0;
        border-top:1px solid #ccc;
        font-size:0.85em;
        color: #555;
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
        text-align: center;
        width: 100%;
    }

    .cabecalho-item {
        flex: 1;
    }

    .cab-left {
        text-align: left;
    }

    .cab-center {
        text-align: center;
    /*    font-weight: bold;*/
    }

    .cab-right {
        text-align: right;
    }
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
        <h1 id="tituloprincipal"><a href=""> <?= htmlspecialchars($titulo) ?> </a></h1>
        <h4 class="slogan"><?= htmlspecialchars($slogan) ?></h4>
        <div class="cabecalho">
            <div class="cabecalho-item cab-left">
                <?php if ($temperatura): ?>São Paulo: <?php if (!empty($nebulosidade)) echo htmlspecialchars($nebulosidade) . ', '; ?><?= htmlspecialchars($temperatura) ?> °C às <?= htmlspecialchars(substr($horarioclima, 0, 2) . "h" . substr($horarioclima, 2, 2)) ?>. <?php endif; ?>
            </div>
            <div class="cabecalho-item cab-center">
                <?= htmlspecialchars($dataExtenso) ?>.
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
    const cardPadding = config.cardpadding || "5px";
    const gapPercent = parseFloat(config.gappercent || 2); // ← lê o valor do JSON
    const cardWidth = (100 - (numColunas - 1) * gapPercent) / numColunas;

    // Função para criar cada item card
    function criarCard(item) {
        if (!item || !item.title) return null;
        const card = document.createElement("div");
        card.classList.add("card");
        card.style.flex = `0 0 ${cardWidth}%`;
        card.style.padding = cardPadding; // ← aplica o padding dinâmico aqui

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
            ${item.chapeu ? `<div class="direita" style="font-size:0.8em;font-weight:bold;text-transform:uppercase;margin: 0 0 6px;">${item.chapeu}</div>` : ""}
            ${imgHTML}
            <div class="card-title">
                <a href="${item.url || '#'}" class="hover" target="_blank" rel="noopener noreferrer">${item.title}</a>
            </div>
            <p class="direita" style="font-size:0.75em;">${dataPubl}</p>
            <div class="card-content">
                <p>${item.location ? `<b>${item.location.toUpperCase()} - </b>` : ""}${item.content || ""} […] 
                ${item.url ? `<a href="${item.url}" class="link hover" target="_blank" rel="noopener noreferrer">Leia mais</a>` : ""}</p>
            </div>`;

        if (item.cardheight) {
            const altura = parseInt(item.cardheight, 10);
            if (!isNaN(altura)) card.style.height = `${altura}px`;
        } else {
            card.style.height = "auto";
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
                i => i && i.type === "reportagem" && i.hidden === false && i.archive === false
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
    async function preencherConteudo(containerId, arquivo, campo = null, valor = null) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container "${containerId}" não encontrado.`);
            document.body.innerHTML += `<p><em>Erro ao carregar o conteúdo.</em></p>`;
            return;
        }

        // Skeletons de carregamento
        container.innerHTML = "";
        for (let i = 0; i < numColunas * 2; i++) {
            const s = document.createElement("div");
            s.classList.add("skeleton");
            s.style.flex = `0 0 ${(100 - (numColunas - 1) * gapPercent) / numColunas}%`;
            s.style.padding = cardPadding; // aplica padding também nos skeletons
            container.appendChild(s);
        }

        try {
            const conteudoJSON = await extrairConteudo(arquivo, campo, valor);
            container.innerHTML = "";

            if (conteudoJSON.length === 0) {
                container.innerHTML = "<p>Nenhum conteúdo disponível.</p>";
                return;
            }

            const fragment = document.createDocumentFragment();
            let linha = document.createElement("div");
            linha.classList.add("linha-cards");

            let countVisible = 0;
            conteudoJSON.forEach((item) => {
                const card = criarCard(item);
                if (card) linha.appendChild(card);

                countVisible++;
                if (countVisible % numColunas === 0) {
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
    }

    // Chamada principal
    preencherConteudo("container-conteudo-01", "conteudo.json", "type", "reportagem");
});
</script>

</body>
</html>
