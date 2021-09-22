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
$image = "";
if (isset($_FILES["image"]["tmp_name"])) {
    if ($_FILES["image"]["size"] >= 8000000)
        die ("L'image dépasse la taille autorisée (8 Mo)");
    elseif ($_FILES["image"]["tmp_name"] != "")
        $image = base64_encode(file_get_contents($_FILES["image"]["tmp_name"]));
}
if (isset($_POST["question"], $_POST["niveau"], $_POST["temps"], $_POST["reponse1"], $_POST["reponse2"], $_POST["reponse3"], $_POST["reponse4"], $_POST["bonnereponse"], $_POST["complement"])) {
$texteQ         = htmlspecialchars($_POST["question"]);
$niveau         = $_POST["niveau"];
$tempsQ         = $_POST["temps"];
$reponse1       = htmlspecialchars($_POST["reponse1"]);
$reponse2       = htmlspecialchars($_POST["reponse2"]);
$reponse3       = htmlspecialchars($_POST["reponse3"]);
$reponse4       = htmlspecialchars($_POST["reponse4"]);
$bonneRep       = $_POST["bonnereponse"];
$explication    = htmlspecialchars($_POST["complement"]);
    if ($bonneRep < 1 OR $bonneRep > 4 OR $reponse4 == "" OR $reponse3 == "" OR $reponse2 == "" OR $reponse1 == "" OR $tempsQ == 0 OR $niveau == 0 OR $texteQ == "") {
        die("Il manque des paramètres");
    }
    $bonneRep1 = $bonneRep2 = $bonneRep3 = $bonneRep4 = false;
    if ($bonneRep == 1)
        $bonneRep1 = true;
    elseif ($bonneRep == 2)
        $bonneRep2 = true;
    elseif ($bonneRep == 3)
        $bonneRep3 = true;
    else
        $bonneRep4 = true;
    
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            SELECT MAX(idQuestion) as "idQuestion"
            FROM Question
SQL);
    $stat->execute();
    $idQuestion = $stat->fetch()['idQuestion'] + 1;
    
    
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            SELECT MAX(idReponse) as "idReponse"
            FROM Reponse
SQL);
    $stat->execute();
    $idReponse = $stat->fetch()['idReponse'];
    
    //Ajout Image
    $idImage = 0;
    if ($image != "") {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
            SELECT MAX(idImage) as "idImage"
            FROM Image
SQL);
        $stat->execute();
        $idImage = $stat->fetch()['idImage']+1;
        $stat = MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO image VALUES (:idImage,:contenu)
SQL);
        $stat->execute([':idImage'=>$idImage,':contenu'=>$image]);
    }
    
    //Ajout Question
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO question VALUES (:idQ,:idI,:idJ,:texte,:temps,1,:explication,:niveau,false)
SQL);
    $idJoueur = $authentication->getUserFromSession()->getId();
    $stat->execute([':idQ'=>$idQuestion,
                    ':idI'=>$idImage,
                    ':idJ'=>$idJoueur,
                    ':texte'=>$texteQ,
                    ':temps'=>$tempsQ,
                    ':explication'=>$explication,
                    ':niveau'=>$niveau]);
    
    //Ajout Réponse
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO reponse VALUES (:idR,:idQ,:bonneRep,:texteRep)
SQL);
    $stat->execute([':idR'=>$idReponse+1,':idQ'=>$idQuestion,':bonneRep'=>$bonneRep1,':texteRep'=>$reponse1]);
    $stat->execute([':idR'=>$idReponse+2,':idQ'=>$idQuestion,':bonneRep'=>$bonneRep2,':texteRep'=>$reponse2]);
    $stat->execute([':idR'=>$idReponse+3,':idQ'=>$idQuestion,':bonneRep'=>$bonneRep3,':texteRep'=>$reponse3]);
    $stat->execute([':idR'=>$idReponse+4,':idQ'=>$idQuestion,':bonneRep'=>$bonneRep4,':texteRep'=>$reponse4]);
    
    http_response_code(302);
    header("Location: .");
    die();

}
else
    die("Il manque des paramètres");