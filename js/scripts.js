/* 	
	*** Modelo de arquivo de código Javascript
	*** para inclusão no elemento HEAD da página HTML.
	*** Desenvolvido por Fábio de Almeida Martins.
*/

/*
var nome = prompt("Digite o seu nome: ");
console.log("nome = " + nome);
if (nome != null && nome != "") 
	alert("Olá, " + nome + "! \nBem vindo à minha página Web!");
*/

$(function () { 
	$("#mudaEstilo").click(function () { 
		var atual = $("#css-light").attr("href"); 
		if (atual === "") { 
			$("#css-light").attr("href", $("#css-light").data("href")); 
			$(this).text('Voltar estilo'); 
		} else { 
			$("#css-light").attr("href", ""); 
			$(this).text('Alterar estilo'); 
		} 
	}); 
}); 

/* 
	Código JS do slider da galeria de imagens
	Fonte: https://tableless.com.br/criando-slideshow-zero-com-javascript-puro-2/
*/
function setaImagem() {
	var settings = {
		primeiraImg: function () {
			elemento = document.querySelector("#slider a:first-child");
			if ((typeof(elemento) != undefined) && (elemento != null)) {
				elemento.classList.toggle("ativo");
				this.legenda(elemento);
			}
		},

		slide: function () {
			elemento = document.querySelector(".ativo");
			if ((typeof (elemento) != undefined) && (elemento != null)) {
				if (elemento.nextElementSibling) {
					elemento.nextElementSibling.classList.toggle("ativo");
					settings.legenda(elemento.nextElementSibling);
					elemento.classList.remove("ativo");
				} else {
					elemento.classList.remove("ativo");
					settings.primeiraImg();
				}
			}
		},

		proximo: function () {
			clearInterval(intervalo);
			elemento = document.querySelector(".ativo");

			if (elemento.nextElementSibling) {
				elemento.nextElementSibling.classList.toggle("ativo");
				settings.legenda(elemento.nextElementSibling);
				elemento.classList.remove("ativo");
			} else {
				elemento.classList.remove("ativo");
				settings.primeiraImg();
			}
			intervalo = setInterval(settings.slide, 4000);
		},

		anterior: function () {
			clearInterval(intervalo);
			elemento = document.querySelector(".ativo");

			if (elemento.previousElementSibling) {
				elemento.previousElementSibling.classList.toggle("ativo");
				settings.legenda(elemento.previousElementSibling);
				elemento.classList.remove("ativo");
			} else {
				elemento.classList.remove("ativo");
				elemento = document.querySelector("a:last-child");
				elemento.classList.toggle("ativo");
				this.legenda(elemento);
			}
			intervalo = setInterval(settings.slide, 4000);
		},

		legenda: function (obj) {
			if ((typeof (obj) != undefined) && (obj != null)) {
				var legenda = obj.querySelector("img").getAttribute("alt");
				document.querySelector("figcaption").innerHTML = legenda;
			}
		}

	}

	//chama o slide
	settings.primeiraImg();

	//chama a legenda
	settings.legenda(elemento);

	//chama o slide à um determinado tempo
	var intervalo = setInterval(settings.slide, 4000);
	var setanext = document.querySelector(".next");
	var setaprev = document.querySelector(".prev");
	if ((typeof (setanext) != undefined) && (setanext != null)) {
		setanext.addEventListener("click", settings.proximo, false);
	}
	if ((typeof (setaprev) != undefined) && (setaprev != null)) {
		setaprev.addEventListener("click", settings.anterior, false);
	}
}
window.addEventListener("load", setaImagem, false);
// fim do código do slider



function exibir(seletor, tipo) {
	elemento.style.visibility = "visible";
}

function ocultar(seletor, tipo) {
	if (seletor == null) {
		elemento = document.getElementByTag("img");
	} else {
		elemento = document.getElementByTag("h1"); //alterar esta parte do código (inserir tratamentos)
	}
	elemento.style.visibility = "hidden";
}

function alteraTexto(idioma) {
	var versiculo01la = "<span id='letra01' class='primletra'>Q</span id='linha01'>ui habitat in adjutorio Altissimi, in protectione Dei caeli commorabitur.</span>";
	var versiculo01en = "<span id='letra01' class='primletra'>L</span id='linha01'>ord, thou hast been our dwelling place in all generations.</span>";
	var versiculo02la = "<span id='letra02' class='primletra'>D</span id='linha02'>icet Domino: Susceptor meus es tu, et refugium meum; Deus meus, sperabo in eum.</span>";
	var versiculo02en = "<span id='letra02' class='primletra'>B</span id='linha02'>efore the mountains were brought forth, or ever thou hasdst formed the earth and the world, even from everlasting to everlasting, thou art God.</span>";
	var versiculo03la = "<span id='letra03' class='primletra'>Q</span id='linha03'>uoniam ipse liberavit me de laqueo venantium, et a verbo aspero. </span>";
	var versiculo03en = "<span id='letra03' class='primletra'>T</span id='linha03'>hou turnest man to destruction; and sayest, Return, ye children of men.</span>";
	var versiculo04la = "<span id='letra04' class='primletra'>S</span id='linha04'>capulis suis obumbrabit tibi, et sub pennis ejus sperabis.</span>";
	var versiculo04en = "<span id='letra04' class='primletra'>F</span id='linha04'>or a thousand years in thy sight are but as yesterday when it is past, and as a watch in the night.</span>";
	var versiculo05la = "<span id='letra05' class='primletra'>S</span id='linha05'>cuto circumdabit te veritas ejus: non timebis a timore nocturno;</span>";
	var versiculo05en = "<span id='letra05' class='primletra'>T</span id='linha05'>hou carriest them away as with a flood; they are as a sleep: in the morning they are like grass which groweth up.</span>";
	var versiculo06la = "<span id='letra06' class='primletra'>A</span id='linha06'> sagitta volante in die, a negotio perambulante in tenebris, ab incursu, et daemonio meridiano.</span>";
	var versiculo06en = "<span id='letra06' class='primletra'>I</span id='linha06'>n the morning it flourisheth, and groweth up; in the evening it is cut down, and withereth.</span>";
	var versiculo07la = "<span id='letra07' class='primletra'>C</span id='linha07'>adent a latere tuo mille, et decem millia a dextris tuis; ad te autem non appropinquabit.</span>";
	var versiculo07en = "<span id='letra07' class='primletra'>F</span id='linha07'>or we are consumed by thine anger, and by thy wrath are we troubled.</span>";
	var versiculo08la = "<span id='letra08' class='primletra'>V</span id='linha08'>erumtamen oculis tuis considerabis, et retributionem peccatorum videbis.</span>";
	var versiculo08en = "<span id='letra08' class='primletra'>T</span id='linha08'>hou hast set our iniquities before thee, our secret sins in the light of thy countenance.</span>";
	var versiculo09la = "<span id='letra09' class='primletra'>Q</span id='linha09'>uoniam tu es, Domine, spes mea; Altissimum posuisti refugium tuum.</span>";
	var versiculo09en = "<span id='letra09' class='primletra'>F</span id='linha09'>or all our days are passed away in thy wrath: we spent our years as a tale that is tols.</span>";
	var versiculo10la = "<span id='letra10' class='primletra'>N</span id='linha10'>on accedet ad te malum, et flagellum non appropinquabit tabernaculo tuo.</span>";
	var versiculo10en = "<span id='letra10' class='primletra'>T</span id='linha10'>he days of our years are threescore years and ten; and if by reason of strength they be fourscore years, yet is their strenght labour and sorrow; for it is soon cut off, and we fly away.</span>";
	var versiculo11la = "<span id='letra11' class='primletra'>Q</span id='linha11'>uoniam angelis suis mandavit de te, ut custodiant te in omnibus viis tuis.</span>";
	var versiculo11en = "<span id='letra11' class='primletra'>W</span id='linha11'>ho knoweth the power of thine anger? even according to thy fear, so is thy wrath.</span>";
	var versiculo12la = "<span id='letra12' class='primletra'>I</span id='linha12'>n manibus portabunt te, ne forte offendas ad lapidem pedem tuum.</span>";
	var versiculo12en = "<span id='letra12' class='primletra'>S</span id='linha12'>o teach us to number our days, that we may apply our hearts unto wisdom.</span>";
	var versiculo13la = "<span id='letra13' class='primletra'>S</span id='linha13'>uper aspidem et basiliscum ambulabis, et conculcabis leonem et draconem.</span>";
	var versiculo13en = "<span id='letra13' class='primletra'>R</span id='linha13'>eturn, O Lord, how long? and let it repent thee concerning thy servants.</span>";
	var versiculo14la = "<span id='letra14' class='primletra'>Q</span id='linha14'>uoniam in me speravit, liberabo eum; protegam eum, quoniam cognovit nomen meum.</span>";
	var versiculo14en = "<span id='letra14' class='primletra'>O</span id='linha14'> satisfy us early with thy mercy; that we may rejoice and be glad all our days.</span>";
	var versiculo15la = "<span id='letra15' class='primletra'>C</span id='linha15'>lamabit ad me, et ego exaudiam eum; cum ipso sum in tribulatione: eripiam eum, et glorificabo eum.</span>";
	var versiculo15en = "<span id='letra15' class='primletra'>M</span id='linha15'>ake us glad acconrdin to the days wherein thou thast afflicted us, and the years wherein we have seen evil.</span>";
	var versiculo16la = "<span id='letra16' class='primletra'>L</span id='linha16'>ongitudine dierum replebo eum, et ostendam illi salutare meum.</span>";
	var versiculo16en = "<span id='letra16' class='primletra'>L</span id='linha16'>et thy work appear unto thy servants, and thy glory unto their children. </span>";
	var versiculo17la = "<span id='letra17' class='primletra'></span id='linha17'>";
	var versiculo17en = "<span id='letra17' class='primletra'>A</span id='linha17'>And let the beauty of the Lord our God be uppon us: and establish thou the work of our hands upon us; yeah, the work of our hands established thou it.</span>";
	switch(idioma) {
		case "ingles":
			document.getElementById("linha01").innerHTML = versiculo01en;
			document.getElementById("linha02").innerHTML = versiculo02en;
			document.getElementById("linha03").innerHTML = versiculo03en;
			document.getElementById("linha04").innerHTML = versiculo04en;
			document.getElementById("linha05").innerHTML = versiculo05en;
			document.getElementById("linha06").innerHTML = versiculo06en;
			document.getElementById("linha07").innerHTML = versiculo07en;
			document.getElementById("linha08").innerHTML = versiculo08en;
			document.getElementById("linha09").innerHTML = versiculo09en;
			document.getElementById("linha10").innerHTML = versiculo10en;
			document.getElementById("linha11").innerHTML = versiculo11en;
			document.getElementById("linha12").innerHTML = versiculo12en;
			document.getElementById("linha13").innerHTML = versiculo13en;
			document.getElementById("linha14").innerHTML = versiculo14en;
			document.getElementById("linha15").innerHTML = versiculo15en;
			document.getElementById("linha16").innerHTML = versiculo16en;
			document.getElementById("linha17").innerHTML = versiculo17en;
			document.getElementById("linha17").visibility = "visible";
			break;
		case "latim":
			document.getElementById("linha01").innerHTML = versiculo01la;
			document.getElementById("linha02").innerHTML = versiculo02la;
			document.getElementById("linha03").innerHTML = versiculo03la;
			document.getElementById("linha04").innerHTML = versiculo04la;
			document.getElementById("linha05").innerHTML = versiculo05la;
			document.getElementById("linha06").innerHTML = versiculo06la;
			document.getElementById("linha07").innerHTML = versiculo07la;
			document.getElementById("linha08").innerHTML = versiculo08la;
			document.getElementById("linha09").innerHTML = versiculo09la;
			document.getElementById("linha10").innerHTML = versiculo10la;
			document.getElementById("linha11").innerHTML = versiculo11la;
			document.getElementById("linha12").innerHTML = versiculo12la;
			document.getElementById("linha13").innerHTML = versiculo13la;
			document.getElementById("linha14").innerHTML = versiculo14la;
			document.getElementById("linha15").innerHTML = versiculo15la;
			document.getElementById("linha16").innerHTML = versiculo16la;
			document.getElementById("linha17").innerHTML = versiculo17la;
			document.getElementById("linha17").visibility = "hidden";
	}
}

function alteraFonteTexto(tipo) {
	var texto1 = document.querySelector("#texto");
	var texto2 = document.getElementsByTagName("p");
	if ((typeof (texto1) == undefined) || (texto1 == null))	alert("Variável texto1 é null ou undefined.");
	else {
		for (let listaclasses of texto1.classList) {
			console.log(listaclasses);
		}
		switch(tipo) {
			case "arial":  
				texto1.className = "arial";
				console.log("Arial");
				break;
			case "brush":
				texto1.className = "brush";
				console.log("Brush");
				break;
			case "calibri":
				texto1.className = "calibri";
				console.log("Calibri");
				break;
			case "century":
				texto1.className = "century";
				console.log("Century Gothic");
				break;
			case "comic-sans":
				texto1.className = "comic-sans";
				console.log("Comic Sans");
				break;
			case "copperplate":
				texto1.className = "copperplate";
				console.log("Copperplate");
				break;
			case "courier-new":
				texto1.className = "courier-new";
				console.log("Courier New");
				break;
			case "freemono":
				texto1.className = "freemono";
				console.log("Freemono");
				break;
			case "freestyle":
				texto1.className = "freestyle";
				console.log("Freestyle");
				break;
			case "futura":
				texto1.className = "futura";
				console.log("Century Futura");
				break;
			case "garamond":
				texto1.className = "garamond";
				console.log("Garamond");
				break;
			case "georgia":
				texto1.className = "georgia";
				console.log("Georgia");
				break;
			case "helvetica":
				texto1.className = "helvetica";
				console.log("Helvetica");
				break;
			case "impact":
				texto1.className = "impact";
				console.log("Impact");
				break;
			case "inkfree":
				texto1.className = "inkfree";
				console.log("Inkfree");
				break;
			case "lucida-cons":
				texto1.className = "lucida-cons";
				console.log("Lucida Console");
				break;
			case "lucida-hand":
				texto1.className = "lucida-hand";
				console.log("Lucida Handwriter");
				break;
			case "monotype":
				texto1.className = "monotype";
				console.log("Monotype Cursiva");
				break;
			case "old-english":
				texto1.className = "old-english";
				console.log("Old English Gothic");
				break;
			case "optima":
				texto1.className = "optima";
				console.log("Optima");
				break;
			case "papyrus":
				texto1.className = "papyrus";
				console.log("Papyrus");
				break;
			case "rockwell":
				texto1.className = "rockwell";
				console.log("Rockwell");
				break;
			case "snell":
				texto1.className = "snell";
				console.log("Snell Roundhand");
				break;
			case "tahoma":
				texto1.className = "tahoma";
				console.log("Tahoma");
				break;
			case "times-new-roman":
				texto1.className = "times-new-roman";
				console.log("Times New Roman");
				break;
			case "trebuchet":
				texto1.className = "trebuchet";
				console.log("Trebuchet");
				break;
			case "verdana":
				texto1.className = "verdana";
				console.log("Verdana");
				break;
		}
	}
}

function alteraTamanhoTexto(tamanho) {
	var texto1 = document.querySelector("#texto");
	var texto2 = document.getElementsByTagName("p");
	if ((typeof (texto1) == undefined) || (texto1 == null)) alert("Variável texto1 é null ou undefined.");
	else {
		switch (tamanho) {
			case "normal":
				texto1.style.fontSize = "1.0em";
				break;
			case "tam11":
				texto1.style.fontSize = "1.1em";
				break;
			case "tam12":
				texto1.style.fontSize = "1.2em";
				break;
			case "tam13":
				texto1.style.fontSize = "1.3em";
				break;
			case "tam14":
				texto1.style.fontSize = "1.4em";
				break;
			case "tam15":
				texto1.style.fontSize = "1.5em";
				break;
			case "tam16":
				texto1.style.fontSize = "1.6em";
				break;
			case "tam17":
				texto1.style.fontSize = "1.7em";
				break;
			case "tam18":
				texto1.style.fontSize = "1.8em";
				break;
			case "tam19":
				texto1.style.fontSize = "1.9em";
				break;
			case "tam20":
				texto1.style.fontSize = "2.0em";
				break;
			case "tam21":
				texto1.style.fontSize = "2.1em";
				break;
			case "tam22":
				texto1.style.fontSize = "2.2em";
				break;
			case "tam23":
				texto1.style.fontSize = "2.3em";
				break;
			case "tam24":
				texto1.style.fontSize = "2.4em";
				break;
			case "tam25":
				texto1.style.fontSize = "2.5em";
				break;
			case "tam26":
				texto1.style.fontSize = "2.6em";
				break;
			case "tam27":
				texto1.style.fontSize = "2.7em";
				break;
			case "tam28":
				texto1.style.fontSize = "2.8em";
				break;
			case "tam29":
				texto1.style.fontSize = "2.9em";
				break;
			case "tam30":
				texto1.style.fontSize = "3.0em";
				break;
			case "tam31":
				texto1.style.fontSize = "3.1em";
				break;
			case "tam32":
				texto1.style.fontSize = "3.2em";
				break;
		}
	}
}

function alteraCorTexto(cor) {
	var texto1 = document.getElementById("texto");
	var texto2 = document.querySelector("#texto");
	var texto2 = document.getElementsByTagName("p");
	if ((typeof (texto1) == undefined) || (texto1 == null)) alert("Variável texto1 é null ou undefined.");
	else {
		texto1.style.color = cor;
	}
}

function alteraCorFundo(cor) {
	var texto1 = document.getElementById("texto");
	var texto2 = document.querySelector("#texto");
	var texto2 = document.getElementsByTagName("p");
	if ((typeof (texto1) == undefined) || (texto1 == null)) alert("Variável texto1 é null ou undefined.");
	else {
		texto1.style.background = cor;
	}
}

/*               ***CONTINUAR DEPOIS***
function ocultarExibir(seletor, tipo) {
	if (seletor == null) {
		elemento = document.getElementByTag("img");
	} else {
		if (seletor == "tag") {
			elemento = document.getElementByTag(tipo); //alterar esta parte do código (inserir tratamentos)
		} else {
			elemento =  
		}
	}

} */