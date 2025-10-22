<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="description" content="descricao">
	<meta name="author" content="Fabio de Almeida Martins">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Texto em quatro colunas - Modelo 01</title>
	<script src="./js/JQuery3-min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">

	<style>
		body {
			margin: 0;
			padding: 0;
			background-color: #fafafa;
			font-family: Arial, sans-serif;
		}
		h1 {
			text-align: center;
			margin: 30px 0;
		}
		.container-colunas {
			width: 90%;
			margin: auto;
		}
		.card {
			border: none;
			border-radius: 8px;
			background: white;
			padding: 10px;
			margin-bottom: 20px;
			height: fit-content;
		}
		.card img {
			width: 100%;
			height: auto;
			border-radius: 8px;
			margin-bottom: 8px;
		}
		.card-title {
			font-weight: bold;
			font-size: 1.1em;
			margin-bottom: 5px;
		}
		.card-text {
			text-align: justify;
			line-height: 1.4;
		}
		.card a {
			color: blue;
			font-weight: bold;
			text-decoration: none;
		}
		.card a:hover {
			text-decoration: underline;
		}
		.barra-superior, .barra-inferior {
			height: 50px;
			width: 100%;
		}
		.barra-superior {
			background: lightgreen;
		}
		.barra-inferior {
			background: pink;
		}
	</style>
</head>

<body>
	<div class="barra-superior"></div>
	<h1>Texto em quatro colunas</h1>

	<div class="container-colunas">
		<div id="cards-container" class="row g-4 justify-content-center">
			<!-- Cards serão carregados aqui -->
		</div>
	</div>

	<div class="barra-inferior"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
	const container = document.getElementById("cards-container");
	container.innerHTML = "<p class='text-center'><em>Carregando conteúdo...</em></p>";

	fetch("conteudo.json")
		.then(response => {
			if (!response.ok) throw new Error("Erro ao carregar conteudo.json");
			return response.json();
		})
		.then(data => {
			container.innerHTML = ""; // limpa mensagem de carregamento
			const conteudos = data.conteudo;

			if (!conteudos || conteudos.length === 0) {
				container.innerHTML = "<p class='text-center'><em>Nenhum conteúdo disponível.</em></p>";
				return;
			}

			// Cria um card para cada item
			conteudos.forEach(item => {
				if (item.type === "reportagem") {
					const col = document.createElement("div");
					col.className = "col-md-3 col-sm-6"; // 4 colunas por linha

					// Imagem com verificação
					const imagem = item.image
						? `<img src="${item.image}" alt="${item.title}">`
						: "";

					// Direitos autorais
					const direitos = item.imgrights
						? `<div style="font-size:0.8em; text-align:right; margin-top:2px;"><em>${item.imgrights}</em></div>`
						: "";

					// Link "Leia mais"
					const link = item.url
						? `<div style="margin-top:10px;"><a href="${item.url}" target="_blank">Leia mais</a></div>`
						: "";

					col.innerHTML = `
						<div class="card">
							${imagem}
							${direitos}
							<div class="card-body">
								${item.title ? `<div class="card-title">${item.title}</div>` : ""}
								${item.content ? `<div class="card-text">${item.content}</div>` : ""}
								${link}
							</div>
						</div>
					`;
					container.appendChild(col);
				}
			});
		})
		.catch(err => {
			console.error(err);
			container.innerHTML = "<p class='text-center text-danger'><em>Erro ao carregar conteúdo.</em></p>";
		});
});
</script>
</body>
</html>
