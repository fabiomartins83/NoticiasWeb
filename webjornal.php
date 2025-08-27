<?php
// Arquivo: exibir_data.php
ini_set('default_charset','UTF-8');
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

    // Define local para português do Brasil
    setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR.UTF-8', 'portuguese');

    // Formata a data usando strftime e UTF-8
    $dataFormatada = str_replace("Feira","feira",str_replace("De","de",mb_convert_case(utf8_encode(strftime('%A, %d de %B de %Y', $dt->getTimestamp())), MB_CASE_TITLE, "UTF-8")));

    // Garante que a string esteja em UTF-8
    return $dataFormatada;
}

$datetimeConfig = obterDataConfig($configFile);
$dataExtenso = formatarDataExtenso($datetimeConfig);
?>

<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="descricao">
    <meta name="author" content="Fabio de Almeida Martins">
    <meta name="copyright" content="Direitos Reservados - Fabio de Almeida Martins - © 2025 ">
    <meta name="reply-to" content="fabiomartins01@gmail.com">
    <meta name="keywords" content="palavras-chaves, palavra-chave">
    <meta name="rating" content="general">
    <meta name="robots" content="noindex,nofollow">
    <meta name="googlebots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="image/x-icon" rel="shortcut icon" href="./img/favicon.ico">
    <link type="application/rss+xml" rel="alternative" title="" href="./feed.rss">
    <link rel="start" href="./home.html">
    <script type="text/javascript" src="./js/JQuery3-min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="./js/scripts.js"></script>
    <title>Jornal Gazeta de Notícias - Homepage</title>
    <link type="text/css" rel="stylesheet" charset="utf-8" href="./css/style-novo.css">
    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <!-- 
    <base href="https://fabiomartins83.github.io/ModeloHTML/" target="_blank">
    <link type="text/css" rel="stylesheet" charset="utf-8" id="css-light" href="" data-href="./css/style-dark.css">
    -->

<style type="text/css">
*, html, main { margin:0; padding:0; box-sizing:border-box; z-index: 0;}
body { height:100%; margin: 0 5%; padding:0; box-sizing:border-box; z-index: 0; line-height:1.3; font-family:'Segoe UI', Tahoma, Geneva, Verdana, Helvetica, sans-serif; background-color: #fff;}
a { color:#00F; font-weight: normal; text-decoration:none; outline:0 none; }
a:link {cursor: pointer;}
a:hover, a:focus { text-decoration:underline; font-weight:bold; color:#00F;}
h1,h2,h3,h4 { text-align:center;}
h5,h6 {text-align:left;}
h1,h2,h3 {font-weight: bold;}
h4,h5,h6 { font-weight:normal; }
h1 { font-size:4em; margin:16px 14px auto; clear:both; }
footer {position: relative; clear: both}
.cabecalho {align-items: center;}
.rodape {position: relative; bottom: 1; right: 1%; display: block; clear: both; /* background-color: #555; */}
.barra-superior {top: 0; display: block; width: 100%; overflow-x: inherit; overflow-y: inherit; z-index: 99; clear: both; text-align: center;}
.barra-inferior {bottom: 0; display: block; width: 100%; overflow-x: inherit; overflow-y: inherit; z-index: 99; clear: both; text-align: center; /* background-color: #444; color: #EEE */}
.logo {display:block; clear: both;}
.direita {text-align: right; justify-content: right;}
.serif {font-family: Georgia, 'Times New Roman', Times, serif; font-weight: normal}
.sans-serif {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Helvetica, sans-serif; font-weight: normal;}
.float-container {display: flex; flex-direction: row; height: fit-content; align-items: center; justify-content: space-between; }
.container-main { /*margin-top:70px; */width:100%; float:left; line-height:normal; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Helvetica, sans-serif; }
.div-principal { width:100%; padding:0; margin:0; text-align:left; height: fit-content; left: 0; align-items: center;}
.container-colunas { display:flex; flex-wrap:wrap; gap:2%; margin: 1%; word-wrap:break-word }
.linha-cards { display:flex; width:100%; gap:2%; margin: 1%;  justify-content:flex-start; word-wrap:break-word; hyphens: auto; }
.card { flex: 1; word-wrap:break-word; hyphens: auto; background: #FFF; border-radius:15px; padding:10px; min-height:200px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 1px 3px rgba(0,0,0,0.15); transition: transform 0.2s, box-shadow 0.2s; }
.card:hover { transform: translateY(-5px); box-shadow:0 4px 10px rgba(0,0,0,0.2); }
.card-img { width:100%; height:auto; border-radius:8px; object-fit: cover; aspect-ratio: 4/3; }
.card-title { word-wrap:break-word; hyphens: auto; font-weight:bold; text-overflow:ellipsis;}
.card-content { word-wrap:break-word; hyphens: auto; flex-grow:1; margin-bottom:10px; font-size:1.1em; text-align:justify; line-height:1.5; }
.card-link {position:relative; bottom: 0%; right: 5%; }
.card-link a { color:#000080; text-decoration:none; font-weight: normal;}
.card-link a:hover { text-decoration:underline; font-weight:bold; }
.card-vazio { background:transparent; color:transparent; border:none; box-shadow:none; visibility:hidden; }
.skeleton { background:#ddd; border-radius:15px; min-height:200px; flex:1; animation:skeleton-loading 1.2s linear infinite alternate; }
.skeleton-text {height: 12px; width: 90%; margin: 5px 0; background: #ddd; border-radius: 6px; animation: skeleton-loading 1.2s linear infinite alternate;}
.nowrap {white-space: nowrap;}
@keyframes skeleton-loading { 0% { background-color:#ddd; } 50% { background-color:#ccc; } 100% { background-color:#ddd; }
}
@font-face {        /* Implementação de fonte gráfica externa */
    font-family: 'Old English Gothic';
    src: url('./css/fonts/OldEnglishGothic.eot?#iefix') format('embedded-opentype'),
        url('./css/fonts/OldEnglishGothic.woff2') format('woff2'),
        url('./css/fonts/OldEnglishGothic.woff') format('woff'),
        url('./css/fonts/OldEnglishGothic.svg#OldEnglishGothic') format('svg');
    font-weight: 100;
    font-style: normal;
}
.fonte-gothic {	font-family: 'Old English Gothic', Garamond, Georgia, 'Times New Roman', Times, serif; }
#tituloprincipal { font-size: 4.5em;}
/* Caixa de busca */
header {
    display:flex;
    justify-content: space-between;
    width: max-content;
    align-items: center;
    margin-bottom: 20px;
}
.search-container {
    position:absolute;
    display: flex;
    width: max-content;
    height: fit-content;
    align-items: center;
    justify-content: space-between;
    right: 0%;
    font-weight: 100;
    font-style:normal;
    font-size: 12px;
    gap: 5px;
}
.search-container input {
    padding: 6px;
    width: 240px;
    border: 1px solid #aaa;
    border-radius: 6px;
}
.search-container button {
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
}
#results {
  margin-top: 20px;
}
#results ul { list-style: none; padding: 0; }
#results li { margin-bottom: 6px 0; }
#results a { color: blue; text-decoration: none; }
#results a:hover {text-decoration: underline;}
.img-container {
    padding: 0;
}
.img-container a {
    color: inherit; /* nunca herda cor de link */
}
.img-container a:hover {
    font-weight: 100;
    text-decoration: none;
    font-style: normal;
}
.img-legenda, .img-descricao {
    font-size: 0.8em;
    color: inherit; /* mantém cor padrao do texto */
}
.img-legenda {
    text-align: right;
    margin: 5px 0;
}
.img-descricao {
    text-align: center;
    padding: 5px 0;
} 
.container-card-title {
    margin: 3px 0;
}
</style>
</head>

<body id="body">
    <main class="container-main">
        <header></header>
        <div class="cabecalho">
            <h1 class="titulo fonte-gothic logo">Gazeta de Notícias</h1>
            <div class="float-container">
                <h4>O que você precisa SABER está aqui</h4>
                <div class="search-container" hidden>
                    <input type="text" id="searchBox" placeholder="" hidden>
                    <button id="searchButton" onclick="buscar()" hidden>Buscar</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="div-principal">
            <h5><?php echo $dataExtenso; ?>. Confira as notícias de hoje:</h5>
            <div id="container-colunas" class="container-colunas"></div>
            <!-- <div id="results"></div> -->
        </div>
        <div id="div-footer" class="div-footer">
            <footer id="footer" class="container-inferior"> <!-- conteúdo da barra inferior -->
                <p id="rodape" class="rodape central"><small>Desenvolvido por Fábio de Almeida Martins<br><b>© 2025 - Fábio de Almeida Martins - Direitos reservados</b></small></p>
            </footer>
        </div>
    </main>

<script async type="text/javascript">
document.addEventListener("DOMContentLoaded", async () => {

    const container = document.getElementById("container-colunas");
    const numColunas = 4;  // número de colunas
    const gapPercent = 2;   // gap em % (padrão = 2%)
    let conteudoJSON = [];
    const cardWidth = (100 - (numColunas - 1) * gapPercent) / numColunas;

    // Função auxiliar para criar card
    async function criarCard(item, fonte="padrao", cor='#000', fundo="#FFF", entrelinha="padrao", alinh="padrao", recuoparag="padrao", margem="padrao", padding="padrao") {
        const card = document.createElement("div");
        card.classList.add("card", "serif");

        if (fonte != "padrao") {card.style.fontFamily = `${fonte}`;}
        if (cor != "padrao") {card.style.color = `${cor}`;}
        if (fundo != "padrao") {card.style.backgroundColor = `${fundo}`;}
        if (entrelinha != "padrao") {card.style.lineHeight = `${entrelinha}`;}
        if (alinh != "padrao") {card.style.textAlign = `${alinh}`;}
        if (recuoparag != "padrao") {card.style.textIndent = `${recuoparag}`;}
        if (margem != "padrao") {card.style.margin = `${margem}`;}
        if (padding != "padrao") {card.style.padding = `${padding}`;}
        
        card.style.flex = `0 0 ${cardWidth}%`;

        let imgHTML = "";

        if(item.image) {
            try {
                const response = await fetch(item.image, { method: "HEAD" });
                if(response.ok){
                    const imagem = `<img src="${item.image}" 
                                    srcset="${item.image} 1x, ${item.image} 2x" 
                                    class="card-img central"
                                    loading="lazy">`;
                    const legenda = item.imgrights ? `<p class="img-legenda" style="color: inherit">Imagem: ${item.imgrights}</p>` : "";
                    if(item.url){
                        imgHTML = `<div class="img-container">
                                    <a href="${item.url}" target="_blank" rel="noopener noreferrer">${imagem}</a>
                                    ${legenda}
                                  </div>`;
                    } else {
                        imgHTML = `<div class="img-container">${imagem}${legenda}</div>`;
                    }
                } else {
                    if(item.imgdescript){
                        imgHTML = `<p class="img-descricao" style="color: inherit;"><em>${item.imgdescript}</em></p>`;
                    }
                }
            } catch(e){
                if(item.imgdescript){
                    imgHTML = `<p class="img-descricao" style="color: inherit;"><em>${item.imgdescript}</em></p>`;
                }
            }
        } else if(item.imgdescript){
            imgHTML = `<p class="img-descricao" style="color: inherit;"><em>${item.imgdescript}</em></p>`;
        }

        // Data e hora de publicação (em português, por extenso)
        let dataPubl = "";
        if (item.datetime) {
            const data = new Date(item.datetime.trim());

            const opcoesData = {day: "numeric", month: "numeric", year: "numeric" };
            // const opcoesData = { weekday: "long", year: "numeric", month: "long", day: "numeric" };
            const opcoesHora = { hour: "2-digit", minute: "2-digit" };

            const dataFormatada = data.toLocaleDateString("pt-BR", opcoesData); // para data abreviada: dd/mm/YYYY, basta desabilitar a const opcoesData
            const horaFormatada = data.toLocaleTimeString("pt-BR", opcoesHora);

            dataPubl = data.toLocaleDateString("pt-BR"); // dataPubl = `${dataFormatada} - ${horaFormatada}`;
        }

        // montagem da estrutura dos card
        card.innerHTML = `
            ${item.chapeu ? `<div class="container-chapeu direita sans-serif" style="text-transform: upper-case;font-weight:bold; margin: 5px;"><span>${item.chapeu.toUpperCase()}</span></div>` : ""}
            ${imgHTML}
            ${item.title ? `<div class="container-card-title"><div class="card-title central" style="overflow-wrap: break-word">${item.title}</div><div class="data-publ"><p class="direita" style="font-weight:normal">${dataPubl}</p></div></div>` : ""}
            ${item.content ? `<div class="card-content" style="overflow-wrap: break-word"> 
                <p><b>${item.location ? `${item.location.toUpperCase()}` + " - " : ""}</b>${item.content}[…]${item.url ? `&nbsp;&nbsp;&nbsp;<a href="${item.url}" class="direita nowrap" target="_blank" rel="noopener noreferrer">Leia mais</a>` : ""}</p> 
            </div>` : ""}
        `;
        return card;
    }

    function criarLinha() {
        const linha = document.createElement("div");
        linha.classList.add("linha-cards");
        return linha;
    }

    // Skeleton loading
    for(let i=0;i<numColunas*2;i++){
        const skeleton = document.createElement("div");
        skeleton.classList.add("skeleton");
        skeleton.style.flex = `0 0 ${cardWidth}%`;
        container.appendChild(skeleton);
    }

    try {
        const response = await fetch("conteudo.json");
        if(!response.ok) throw "Não foi possível carregar o conteúdo.";
        const data = await response.json();
        container.innerHTML = "";
        conteudoJSON = Object.values(data.conteudo).filter(item => item.type==="reportagem");    // conteudoJSON = data.filter(item => item.type === "reportagem"); 
        let linhaAtual = criarLinha();

        // Cria todos os cards com await
        const cardsPromises = conteudoJSON.map(item => criarCard(item));
        const cards = await Promise.all(cardsPromises);

        cards.forEach((card, index) => {
            if(index % numColunas === 0 && index!==0){
                container.appendChild(linhaAtual);
                linhaAtual = criarLinha();
            }
            linhaAtual.appendChild(card);
        });
        container.appendChild(linhaAtual);

        // Preenche última linha com cards invisíveis
        const itensNaUltimaLinha = conteudoJSON.length % numColunas;
        if(itensNaUltimaLinha > 0){
            const excedente = numColunas - itensNaUltimaLinha;
            for(let i=0;i<excedente;i++){
                const cardVazio = document.createElement("div");
                cardVazio.classList.add("card","card-vazio");
                cardVazio.style.flex = `0 0 ${cardWidth}%`;
                linhaAtual.appendChild(cardVazio);
            }
        }
    } catch(err){
        console.error(err);
        container.innerHTML = "<em>Erro ao carregar conteúdo.</em>";
    }

    // Busca nos campos title, content, abstract e tema
    document.getElementById("searchBox").addEventListener("input", function(){
        const termo = this.value.toLowerCase();
        const resultadosDiv = document.getElementById("results");
        if(termo.length < 2){
            resultadosDiv.innerHTML = "";
            return;
        }
        const resultados = conteudoJSON.filter(item =>
            (item.title && item.title.toLowerCase().includes(termo)) ||
            (item.content && item.content.toLowerCase().includes(termo)) ||
            (item.abstract && item.abstract.toLowerCase().includes(termo)) ||
            (item.tema && item.tema.toLowerCase().includes(termo))
        );
        if(resultados.length===0){
            resultadosDiv.innerHTML = "<p>Busca finalizada. O conteúdo não foi localizado.</p>";
            return;
        }
        const lista = document.createElement("ul");
        resultados.forEach(item=>{
            const li = document.createElement("li");
            const link = document.createElement("a");
            link.href = item.url || "#";
            link.textContent = item.url || "URL não definida.";
            li.appendChild(link);
            lista.appendChild(li);
        });
        resultadosDiv.innerHTML = "";
        resultadosDiv.appendChild(lista);
    });
});
</script>

<!--
<label for="filtro-editoria">Filtrar por editoria:</label>      // código para filtrar por editoria (inserir no HTML)
<select id="filtro-editoria">
  <option value="">Todas</option>
</select>

<script>
async function carregarEditorias() {
  try {
    const response = await fetch("editorias.json");
    const data = await response.json();

    const select = document.getElementById("filtro-editoria");

    data.editoria.forEach(editoria => {
      const option = document.createElement("option");
      option.value = editoria;
      option.textContent = editoria;
      select.appendChild(option);
    });

    // Evento de filtro
    select.addEventListener("change", () => {
      const filtro = select.value;
      filtrarNoticiasPorEditoria(filtro);
    });

  } catch (error) {
    console.error("Erro ao carregar editorias:", error);
  }
}

// Exemplo de função de filtro (você pode adaptar ao seu código de cards)
function filtrarNoticiasPorEditoria(editoria) {
  const cards = document.querySelectorAll(".card");
  cards.forEach(card => {
    if (!editoria || card.dataset.editoria === editoria) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}

carregarEditorias();
</script>
-->

</body>
</html>