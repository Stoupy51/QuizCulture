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
    // Rediriger vers le formulaire de connexion
    http_response_code(302);
    header("Location: .");
    die();
}


/**
 * Statistiques de Questions
 */
$statQ = "";
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idQuestion) as "n", SUM(tempsQuestion) as "TempsTotal", ROUND(AVG(tempsQuestion),2) as "TempsMoyen"
    FROM Question
    WHERE isVerified = 1
SQL);
$stat->execute();
$ligne = $stat->fetch();
$nbQuestion = $ligne["n"];
$tempsTotal = $ligne["TempsTotal"];
$tempsMoyen = $ligne["TempsMoyen"];

//Pourcentages selon le niveau
$pourcentages = "";
for ($i=1; $i <= 10; $i++) {
    $stat = MyPDO::getInstance()->prepare(<<<SQL
        SELECT COUNT(idQuestion) as "n"
        FROM Question
        WHERE niveau = :niv
        AND isVerified = 1
    SQL);
    $stat->execute([":niv"=>$i]);
    $pourcentQ = round($stat->fetch()["n"]*100/$nbQuestion);
    $pourcentages .= <<<HTML
                    Pourcentage de question de niveau $i : $pourcentQ%<br>

HTML;
}

//Nombre de question à choix
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idQuestion) as "n"
    FROM Question
    WHERE isVerified = 1
    AND typeReponse = 1
SQL);
$stat->execute();
$nbQuestionChoix = $stat->fetch()["n"];

//Nombre de question écrit
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idQuestion) as "n"
    FROM Question
    WHERE isVerified = 1
    AND typeReponse = 0
SQL);
$stat->execute();
$nbQuestionRep = $stat->fetch()["n"];

$statQ = <<<HTML
                    <p class="justify-content-center text-center px-2"> Nombre de question vérifiées : {$nbQuestion}</p>
                    <p class="justify-content-center text-center px-2"> Nombre de question à choix : {$nbQuestionChoix}</p>
                    <p class="justify-content-center text-center px-2"> Nombre de question à réponse : {$nbQuestionRep}</p>
                    <p class="justify-content-center text-center px-2"> Temps Total des questions : {$tempsTotal} secondes</p>
                    <p class="justify-content-center text-center px-2"> Temps Moyen des questions : {$tempsMoyen} secondes</p>
                    <p class="justify-content-center text-left px-2">
$pourcentages                    </p>
HTML;
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////

/**
 * Statistiques de Réponses
 */
$statR = "";
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idReponse) as "n"
    FROM Reponse
SQL);
$stat->execute();
$nbReponse = $stat->fetch()["n"];

//Nombre de bonnes réponses
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idReponse) as "n"
    FROM Reponse
    WHERE bonneReponse = 1
SQL);
$stat->execute();
$nbBonnesReponses = $stat->fetch()["n"];

//Nombre de mauvaises réponses
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idReponse) as "n"
    FROM Reponse
    WHERE bonneReponse = 0
SQL);
$stat->execute();
$nbMauvaisesReponses = $stat->fetch()["n"];

$statR = <<<HTML
                    <p class="justify-content-center text-center px-2"> Nombre de réponses : {$nbReponse}</p>
                    <p class="justify-content-center text-center px-2"> Nombre de bonnes réponses : {$nbBonnesReponses}</p>
                    <p class="justify-content-center text-center px-2"> Nombre de mauvaises réponses : {$nbMauvaisesReponses}</p>
HTML;
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////

/**
 * Statistiques de Réponses
 */
$statJ = "";
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idJoueur) as "n", SUM(points) as "SUM", ROUND(AVG(points),2) as "AVG"
    FROM Joueur
SQL);
$stat->execute();
$ligne = $stat->fetch();
$nbJoueur = $ligne["n"];
$totalPoints = $ligne["SUM"];
$moyPoints = $ligne["AVG"];

//Nombre de modérateurs
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT COUNT(idJoueur) as "n"
    FROM Joueur
    WHERE role = 1
SQL);
$stat->execute();
$nbModo = $stat->fetch()["n"];
$nbNonModo = $nbJoueur-$nbModo;

$statJ = <<<HTML
                    <p class="justify-content-center text-center px-2"> Nombre de Joueurs : {$nbJoueur}</p>
                    <p class="justify-content-center text-center px-2"> Nombre de Modérateurs : {$nbModo}</p>
                    <p class="justify-content-center text-center px-2"> Points totaux des Joueurs : {$totalPoints}</p>
                    <p class="justify-content-center text-center px-2"> Points moyens des Joueurs : {$moyPoints}</p>
HTML;
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////

$page = new WebPage("Accueil");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="css/admin.css">
HTML);

$page->appendContent(<<<HTML
        <h1 class="button mt-3 mx-auto p-2 text-center d-flex flex-column justify-content-center w-50">Pataïtaï</h1>
        <div class="d-flex flex-row justify-content-center">
            <div class="d-flex mt-5 d-flex flex-column w-25">
                <p class="button mx-auto mt-2 justify-content-center twrap text-center">Statistiques Questions</p>
                <div class="button mx-5 my-4 d-flex flex-column justify-content-center">
$statQ
                </div>
            </div>
            <div class="d-flex mt-5 d-flex flex-column w-25">
                <p class="button mx-auto mt-2 justify-content-center twrap text-center">Statistiques Reponses</p>
                <div class="button mx-5 my-4 d-flex flex-column justify-content-center">
$statR
                </div>
            </div>
            <div class="d-flex mt-5 d-flex flex-column w-25">
                <p class="button mx-auto mt-2 justify-content-center twrap text-center">Statistiques Joueurs</p>
                <div class="button mx-5 my-4 d-flex flex-column justify-content-center">
$statJ
                </div>
            </div>
        </div>
HTML);

echo $page->toHTML();
