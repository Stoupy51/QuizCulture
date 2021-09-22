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

if (isset($_GET["idQuestion"])) {
    $idQuestion = $_GET["idQuestion"];
}
else {
    die("Aucune question à cet ID n'existe");
}

$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT pseudo, texte, idImage, niveau, tempsQuestion, explication, typeReponse
    FROM Question q, Joueur j
    WHERE q.idJoueur = j.idJoueur
    AND q.idQuestion = :id
SQL);
$stat->execute([":id"=>$idQuestion]);
$all = $stat->fetch();
$pseudo = $all["pseudo"];
$texte = $all["texte"];
$idImage = $all["idImage"];
$niveau = $all["niveau"];
$temps = $all["tempsQuestion"];
$explication = $all["explication"];
$typeReponse = $all["typeReponse"];
if ($typeReponse != 0) {
    // Rediriger vers la page d'accueil
    http_response_code(302);
    header("Location: moderation.php");
    die();
}
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT texteReponse, idReponse
    FROM Reponse
    WHERE idQuestion = :id
SQL);
$stat->execute([":id"=>$idQuestion]);
$all = $stat->fetchAll();
$reponses = htmlspecialchars($all[0]["texteReponse"]);
for ($i=1; $i < Count($all); $i++) {
    $r = htmlspecialchars($all[$i]["texteReponse"]);
    $reponses .= ", $r";
}

$selectNiv = "";
for ($i=1; $i<=10; $i++) {
    if ($niveau == $i)
        $selectNiv .= <<<HTML
                            <option value="$i" selected>$i</option>
HTML;
    else $selectNiv .= <<<HTML
                            <option value="$i">$i</option>
HTML;
}

$selectSecond = "";
$tab = [10,15,20,30];
foreach ($tab as $k => $v) {
    if ($temps == $v)
        $selectSecond .= <<<HTML
                            <option value="$v" selected>$v</option>
HTML;
    else $selectSecond .= <<<HTML
                            <option value="$v">$v</option>
HTML;
}





$page = new WebPage("Modération Réponse");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/reponse.css" rel="stylesheet">
HTML);

$page->appendContent(<<<HTML
        <div class="d-flex flex-column w-50 m-auto">
            <form class="d-flex flex-column" name="AddQuestion" method="POST" action="editReponse.php" enctype="multipart/form-data">
                <div class="button2 mt-5 p-2 text-center">
                    <h2>Par $pseudo</h2>
                </div>
                <label class="button2 mt-5 p-2 text-center">
                    <input class="w-100" name="question" type="text" placeholder="Tapez votre question ici" maxlength="100" value="$texte" required>
                </label>                   
                <label class="d-flex flex-column align-items-center mx-auto button2 mt-5 text-center w-75">
                    <img class="mt-2" src="imageQuestion.php?id=$idImage" height="150">
                    <div class="button3 mt-5 px-3 p-2">
                        <h2 style="font-size: 1em;">Modifier l'image</h2>
                    </div>
                    <input name="image" type="file" accept=".png, .jpg, .jpeg" class="m-4">
                </label>
                <div class="d-flex flex-row justify-content-between">
                    <label class="d-flex button liste mt-1 p-2 text-center">
                        <img src="img/trophy.png"  width="25">
                        <select class="sel" name="niveau" required>
                            <option disabled selected label="Niveau"></option>
$selectNiv
                        </select>
                    </label>
                    <label class="button liste mt-1 p-2 text-center">
                        <img src="img/horloge.png" width="25">
                        <select class="sel" name="temps" required>
                            <option disabled selected label="Temps (en secondes)"></option>
$selectSecond
                        </select>
                    </label>
                </div>
                <div class="mt-3 rep">Réponses acceptées:</div>
                <label class="d-flex button mx-3 p-2 mt-5 text-center align-items-center">
                    <input class="m-2 p-3 left w-100" name="reponses" type="text" placeholder="Ajouter des réponses sous ce format : Reponse1, Reponse2, Reponse3, ..." value="$reponses" required>
                </label>
                <label class="button2 mt-5 p-2">
                    <input class="left w-100 mb-5" name="complement" type="text" placeholder="Ajouter des infos complémentaires (optionnel)" value="$explication" maxlength="200">
                </label>
                <div class="d-flex justify-content-between">
                    <a class="m-5 p-2 px-3 buttonsupprimer" onclick="location.href='deleteQuestion.php?idQuestion=$idQuestion';">Supprimer</a>
                    <button class="m-5 p-2 px-3 buttonvalider" type="submit">Valider</button>
                </div>
                <input name="idQuestion" type="hidden" value="$idQuestion">
            </form>
        </div>
HTML);

echo $page->toHTML();