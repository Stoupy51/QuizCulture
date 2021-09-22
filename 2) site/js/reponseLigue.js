function b64DecodeUnicode(str) {
    return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
    }).join(''))
}

var splits = b64DecodeUnicode(bonnesReponses);
splits = splits.split(", ");




//Construire le timer
document.getElementById('timer').textContent = time;

function countdown() {
	document.getElementById('timer').textContent = time;
	time--;
	setTimeout(countdown, 1000);
	if (time == -1) {
		selected = true;
		pageReponse();
	}
}
countdown();

function written(e) {
	if (e.key === 'Enter') {
		var input = document.getElementById('reponse');
		var rep = input.value.toUpperCase();
		if (splits.includes(rep)) {
			var bon = true;
		}
		else {
			var bon = false;
		}
		pageReponse(bon);
	}
}

function pageReponse(bon) {
	stopTimer = true;
	let d = document.getElementById("buttonTemps");
	let timer = document.getElementById("temps");
	d.removeChild(timer);
	if (bon == true) {
		document.getElementById('reponse').style.border = '0.5rem solid green';
	}
	else {
		document.getElementById('reponse').style.border = '0.5rem solid red';
	}
	let bonneRep = document.getElementById('reponse');
	bonneRep.value = splits[0].toLowerCase();
	bonneRep.value = bonneRep.value[0].toUpperCase()+bonneRep.value.substring(1);
	bonneRep.type = "visible";
	bonneRep.class = bonneRep.class+"bonneReponse";


	if (expComplementaires != "") {
		let div = document.getElementById('ExpComp');
		let explications = document.createElement("span");
		explications.innerHTML = expComplementaires+"<br>"+submitedBy;
		div.appendChild(explications);
		div.classList.add("explications");
	}
    else {
		let div = document.getElementById('ExpComp');
		let explications = document.createElement("span");
		explications.innerHTML = submitedBy;
		div.appendChild(explications);
		div.classList.add("explications");
	}
	let quitter = document.querySelector(".buttonquitter");
	quitter.style.display = 'block';
	let suivant = document.querySelector(".buttonsuivant");
	suivant.style.display = 'block';

}