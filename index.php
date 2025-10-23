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

// cria a variável $anonumero, que armazena Ano, Número e Edição 
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
<style>
/* --- CSS incorporado (jornalweb.css) --- */
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
.cabecalho {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
    margin-top: 10px;
    font-weight: normal;
    text-height: 1;
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
        <h1 id="tituloprincipal"><?= htmlspecialchars($titulo) ?></h1>
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
    const numColunas = <?= (int)$colunas ?>;
    const gapPercent = 2;
    const cardWidth = (100 - (numColunas - 1) * gapPercent) / numColunas;

    // Função para criar o HTML de cada card
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
                    ${item.imgrights ? `<p class="img-legenda"><em>${item.imgrights}</em></p>` : ""}
                </div>`;
        } else if (item.imgdescript) {
            imgHTML = `<p class="img-descricao"><em>${item.imgdescript}</em></p>`;
        }

        const dataPubl = item.datetime ? new Date(item.datetime).toLocaleDateString("pt-BR") : "";

        card.innerHTML = `
            ${item.chapeu ? `<div class="direita" style="font-size:0.8em;font-weight:bold;text-transform:uppercase;margin-bottom:4px;">${item.chapeu}</div>` : ""}
            ${imgHTML}
            <div class="card-title">
                <a href="${item.url || '#'}" target="_blank" rel="noopener noreferrer">${item.title}</a>
            </div>
            <p class="direita" style="font-size:0.75em;">${dataPubl}</p>
            <div class="card-content">
                <p>${item.location ? `<b>${item.location.toUpperCase()} - </b>` : ""}${item.content || ""} […] 
                ${item.url ? `<a href="${item.url}" class="link" target="_blank" rel="noopener noreferrer">Leia mais</a>` : ""}</p>
            </div>`;

        card.style.flex = `0 0 ${cardWidth}%`;
        if (item.cardheight) {
            const altura = parseInt(item.cardheight, 10);
            if (!isNaN(altura)) card.style.height = `${altura}px`;
        } else {
            card.style.height = 'auto';
        }

        return card;
    }

    // Função para ler o arquivo JSON
    async function extrairConteudo(arquivo, campo = null, valor = null) {
        try {
            const responseJson = await fetch(arquivo);
            if (!responseJson.ok) throw new Error("Falha ao carregar conteúdo JSON.");

            const data = await responseJson.json();
            let conteudoArquivo = Object.values(data.conteudo || {}).filter(
                i => i && i.type === "reportagem" && i.hidden === false && i.archive === false
            );

            // filtro opcional por campo/valor
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

    // 🧩 NOVA FUNÇÃO: monta as linhas e cards dinamicamente
    async function preencherConteudo(containerId, arquivo, campo = null, valor = null) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container "${containerId}" não encontrado.`);
            // Exibe mensagem na página
            document.body.innerHTML += `<p><em>Erro ao carregar o conteúdo.</em></p>`;
            return;
        }

        // skeletons de carregamento
        container.innerHTML = "";
        for (let i = 0; i < numColunas * 2; i++) {
            const s = document.createElement("div");
            s.classList.add("skeleton");
            s.style.flex = `0 0 ${(100 - (numColunas - 1) * 2) / numColunas}%`;
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

    // --- Chamada prática (substitui o código antigo direto) ---
    preencherConteudo("container-conteudo-01", "conteudo.json", "type", "reportagem");
});
</script>

</body>
</html>
