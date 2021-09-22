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
if ($authentication->getUserFromSession()->getRole() == 0) {
    // Rediriger vers la page d'accueil
    http_response_code(302);
    header("Location: /.");
    die();
}

$page = new WebPage("Modération");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/moderation.css" rel="stylesheet">
HTML);

//Stats
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idQuestion) as "n"
    FROM Question
    WHERE isVerified = 0
SQL);
$stat->execute();
$nbQuestion = $stat->fetch()["n"];

$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idQuestion) as "n"
    FROM Question
    WHERE isVerified = 0
    AND typeReponse = 1
SQL);
$stat->execute();
$nbQuestionChoix = $stat->fetch()["n"];

$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idQuestion) as "n"
    FROM Question
    WHERE isVerified = 0
    AND typeReponse = 0
SQL);
$stat->execute();
$nbQuestionRep = $stat->fetch()["n"];
//

$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT texte, typeReponse, idQuestion, pseudo, niveau
    FROM Question q, Joueur j
    WHERE q.idJoueur = j.idJoueur
    AND isVerified = 0
    ORDER BY idQuestion
SQL);
$stat->execute();

$typeReponse = ["Écrit","Proposition"];
$linkReponse = ["Reponse","Choix"];
$content = "";
foreach ($stat->fetchAll() as $k => $v) {
    $indexRep = $v["typeReponse"];
    $id = $v["idQuestion"];
    $pseudo = $v["pseudo"];
    $niv = $v["niveau"];
    $content .= <<<HTML
            <div class="d-flex flex-row w-100 px-5 mt-2">
                <div class="question propo px-2 flex-grow-1"><a href="moderation{$linkReponse[$indexRep]}.php?idQuestion=$id">$id ➤ {$v["texte"]}</a></div>
                <div class="question propo ml-2 px-2"><a>$niv</a></div>
                <div class="etat propo mx-2 px-2 w-25"><a class="text-primary">{$typeReponse[$indexRep]} par $pseudo</a></div>
            </div>
HTML;
}


$page->appendContent(<<<HTML
    <div class="d-flex flex-column w-75 m-auto">
        <h1 class="button mt-2 mx-auto mb-4 p-1 text-center d-flex flex-column justify-content-between w-75">Pataïtaï</h1>
        <h5 class="question mt-2 mx-4 mb-4 p-1 justify-content-between w-50 text-info">
            ➤ Nombre de questions sur cette page : $nbQuestion<br>
            ➤ Nombre de questions de type Proposition : $nbQuestionChoix<br>
            ➤ Nombre de questions de type Écrit : $nbQuestionRep<br>
        </h5>
        <div class="d-flex flex-column">
$content
        </div>
        <div class="m-5 justify-content-center">
                <a class="p-1 buttonquitter" href="."><svg width="26" height="27" viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.8 16.875V13.5H12.1333V10.125H20.8V6.75L26 11.8125L20.8 16.875ZM19.0667 15.1875V21.9375H10.4V27L0 21.9375V0H19.0667V8.4375H17.3333V1.6875H3.46667L10.4 5.0625V20.25H17.3333V15.1875H19.0667Z" fill="white"/>
                        </svg>
                        Quitter    
                </a>
        </div>
    </div>
HTML);

echo $page->toHTML();