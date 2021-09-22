<?php declare(strict_types=1);
require_once "autoload.php";
$authentication = new UserAuthentication();

// Un utilisateur est-il connecte ?
if (!$authentication->isUserConnected()) {
    // Rediriger vers le formulaire de connexion
    http_response_code(302);
    header("Location: connection.html");
    die();
}

$page = new WebPage("Proposez vos questions");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/submit.css" rel="stylesheet">
HTML);

$page->appendContent(<<<HTML
    <div class="d-flex flex-column w-50 m-auto">
            <h2 class="title mt-5 mx-5 p-3 text-center d-flex flex-column">Choisissez le type de réponse</h2>

        <div class="d-flex flex-column">
            <div class="d-flex m-3 justify-content-center">
                <div class=" mx-5 mt-5 d-flex flex-column">
                    <button class="buttoncolor button2 connect text-center p-2" onclick="location.href='submitReponse.html';">Réponse écrite</button>
                    <div class="button mx-3 mt-3">
                        <p class="twrap p-2 m-auto">Il faudra écrire directement la bonne réponse au clavier</span>
                    </div>
                </div>
                <div class=" mx-5 mt-5 d-flex flex-column">
                    <button class="buttoncolor button2 connect text-center p-2" onclick="location.href='submitChoix.html';">Réponse à choix</button>
                    <div class="button mx-3 mt-3">
                        <p class="twrap p-2 m-auto">Il faudra choisir une seule bonne proposition parmi 4 réponses possibles</span>
                    </div>
                </div>
            </div>
                <div class="d-flex flex-row">
                    <div class="mx-3 mt-3 p-1"  onclick="location.href='.';">
                    <a class="m-5 p-2 buttonquitter" href="."><svg width="26" height="27" viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.8 16.875V13.5H12.1333V10.125H20.8V6.75L26 11.8125L20.8 16.875ZM19.0667 15.1875V21.9375H10.4V27L0 21.9375V0H19.0667V8.4375H17.3333V1.6875H3.46667L10.4 5.0625V20.25H17.3333V15.1875H19.0667Z" fill="white"/>
                        </svg>
                        Quitter
                    </a>
                    </div>
                </div>
        </div>
    </div>
HTML);






echo $page->toHTML();
