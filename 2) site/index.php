<?php declare(strict_types=1);
require_once "autoload.php";

$page = new WebPage("Accueil");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/index.css" rel="stylesheet">
HTML);

$authentication = new UserAuthentication();
if (!$authentication->isUserConnected()) {
    $page->appendContent(<<<HTML
        <div class="d-flex flex-column">
            <h1 class="button mt-3 mx-auto mb-5 p-2 text-center d-flex flex-column justify-content-between w-50">Pataïtaï</h1>
            <div class="d-flex mt-5 mr-2 justify-content-center">
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='connection.html';">
                    <p class="mx-4 twrap text-center">Jouer en mode Ligue</p>
                    <div class="d-flex connect justify-content-around">
                        <img src="img/lock.png" class="lock">
                        <p class="m-0 p-0">Connectez-vous</p>
                        <img src="img/lock.png" class="lock">
                    </div>
                </div>
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='normal.php';">
                    <p class="mx-4 twrap text-center">Jouer en mode Normal</p>
                </div>
            </div>
            <div class="d-flex mt-5 mr-2 justify-content-center">
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='connection.html';">
                    <p class="mx-4 twrap text-center">Consulter son classement</p>
                    <div class="d-flex connect justify-content-around">
                            <img src="img/lock.png" class="lock">
                            <p class="m-0 p-0">Connectez-vous</p>
                            <img src="img/lock.png" class="lock">
                    </div>
                </div>
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='connection.html';">
                    <p class="mx-4 twrap text-center">Créer une question</p>
                    <div class="d-flex connect justify-content-around">
                        <img src="img/lock.png" class="lock">
                        <p class="m-0 p-0">Connectez-vous</p>
                        <img src="img/lock.png" class="lock">
                    </div>
                    
                </div>
            </div>
            <div class="d-flex mt-5 mr-2 justify-content-center">
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='connection.html';">
                    <p class="mx-4 twrap text-center">Paramètres</p>
                    <div class="d-flex connect justify-content-around">
                        <img src="img/lock.png" class="lock">
                        <p class="m-0 p-0">Connectez-vous</p>
                        <img src="img/lock.png" class="lock">
                    </div>
                </div>
                <div class ="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='aide.html';">
                    <p class="mx-4 twrap text-center">Aide</p>
                </div>
            </div>
        </div>
HTML);
}
else {
    $modo = "";
    if ($authentication->getUserFromSession()->getRole() != 0)
        $modo = <<<HTML
        <div class="d-flex mt-5 mr-2 justify-content-center">
            <div class="button vert mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='moderation.php';">
                <p class="mx-4 twrap text-center">Modération des questions non vérifiées</p>
            </div>
            <div class ="button vert mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='moderation2.php';">
                <p class="mx-4 twrap text-center">Modération des questions vérifiées</p>
            </div>
        </div>
HTML;
    $page->appendContent(<<<HTML
        <div class="d-flex flex-column">
            <h1 class="button mt-3 mx-auto mb-5 p-2 text-center d-flex flex-column justify-content-between w-50">Pataïtaï</h1>
            <div class="d-flex mt-5 mr-2 justify-content-center">
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='ligue.php';">
                    <p class="mx-4 twrap text-center">Jouer en mode Ligue</p>
                </div>
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='normal.php';">
                    <p class="mx-4 twrap text-center">Jouer en mode Normal</p>
                </div>
            </div>
            <div class="d-flex mt-5 mr-2 justify-content-center">
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='classement.php';">
                    <p class="mx-4 twrap text-center">Consulter son classement</p>
                </div>
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='submit.php';">
                    <p class="mx-4 twrap text-center">Créer une question</p>
                </div>
            </div>
            <div class="d-flex mt-5 mr-2 justify-content-center">
                <div class="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='settings.php';">
                    <p class="mx-4 twrap text-center">Paramètres</p>
                </div>
                <div class ="button mx-5 mt-2 d-flex flex-column justify-content-center" onclick="location.href='aide.html';">
                    <p class="mx-4 twrap text-center">Aide</p>
                </div>
            </div>
$modo
        </div>
HTML);
}

echo $page->toHTML();
