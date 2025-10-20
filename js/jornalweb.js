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