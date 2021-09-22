var bonneReponse = atob(bonnesReponses);

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



var reponse1 = document.getElementById('reponse1');
var reponse2 = document.getElementById('reponse2');
var reponse3 = document.getElementById('reponse3');
var reponse4 = document.getElementById('reponse4');

reponse1.onclick = test1;
reponse2.onclick = test2;
reponse3.onclick = test3;
reponse4.onclick = test4;

var selected = false;

function test1() {
	if (selected !== false) {} else {
		selected = true;
		var button = document.getElementById('reponse1');
		var rep = button.textContent;
		if (rep == bonneReponse) {
			button.style.backgroundColor = 'green';
		} else {
			button.style.backgroundColor = 'red';
		}
		pageReponse();
	}
}

function test2() {
	if (selected !== false) {} else {
		selected = true;
		var button = document.getElementById('reponse2');
		var rep = button.textContent;
		if (rep == bonneReponse) {
			button.style.backgroundColor = 'green';
		} else {
			button.style.backgroundColor = 'red';
		}
		pageReponse();
	}
}

function test3() {
	if (selected !== false) {} else {
		selected = true;
		var button = document.getElementById('reponse3');
		var rep = button.textContent;
		if (rep == bonneReponse) {
			button.style.backgroundColor = 'green';
		} else {
			button.style.backgroundColor = 'red';
		}
		pageReponse();
	}
}

function test4() {
	if (selected !== false) {} else {
		selected = true;
		var button = document.getElementById('reponse4');
		var rep = button.textContent;
		if (rep == bonneReponse) {
			button.style.backgroundColor = 'green';
		} else {
			button.style.backgroundColor = 'red';
		}
		pageReponse();
	}
}

function pageReponse() {
	let d = document.getElementById("buttonTemps");
	let timer = document.getElementById("temps");
	d.removeChild(timer);
	if (bonneReponse == document.getElementById('reponse1').textContent) {
		document.getElementById('reponse1').style.border = '0.5rem solid green';
	}
	if (bonneReponse == document.getElementById('reponse2').textContent) {
		document.getElementById('reponse2').style.border = '0.5rem solid green';
	}
	if (bonneReponse == document.getElementById('reponse3').textContent) {
		document.getElementById('reponse3').style.border = '0.5rem solid green';
	}
	if (bonneReponse == document.getElementById('reponse4').textContent) {
		document.getElementById('reponse4').style.border = '0.5rem solid green';
	}
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

function upPoints() {
	
}