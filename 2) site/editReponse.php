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
$image = "";
if (isset($_FILES["image"]["tmp_name"])) {
    if ($_FILES["image"]["size"] >= 8000000)
        die ("L'image dépasse la taille autorisée (8 Mo)");
        elseif ($_FILES["image"]["tmp_name"] != "")
        $image = base64_encode(file_get_contents($_FILES["image"]["tmp_name"]));
}
if (isset($_POST["question"], $_POST["niveau"], $_POST["temps"], $_POST["reponses"], $_POST["complement"], $_POST["idQuestion"])) {
$texteQ         = htmlspecialchars($_POST["question"]);
$niveau         = $_POST["niveau"];
$tempsQ         = $_POST["temps"];
$reponses       = explode(', ', $_POST["reponses"]);
$explication    = htmlspecialchars($_POST["complement"]);
$idQuestion     = $_POST["idQuestion"];
    if (Count($reponses) < 1 OR $tempsQ == 0 OR $niveau == 0 OR $texteQ == "") {
        die("Il manque des paramètres");
    }

    $stat = MyPDO::getInstance()->prepare(<<<SQL
        SELECT idImage
        FROM Question
        WHERE idQuestion = :id
SQL);
    $stat->execute([':id'=>$idQuestion]);
    $idImage = $stat->fetch()['idImage'];
    //Update Image
    if ($image != "") {
        if ($idImage != 0) {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
            UPDATE image SET contenu = :contenu
            WHERE idImage = :idImage
SQL);
        }
        else {
            $stat = MyPDO::getInstance()->prepare(<<<SQL
            SELECT MAX(idImage) as "idImage"
            FROM Image
SQL);
            $stat->execute();
            $idImage = $stat->fetch()['idImage']+1;
            $stat = MyPDO::getInstance()->prepare(<<<SQL
                INSERT INTO image VALUES (:idImage,:contenu)
SQL);
        }
        $stat->execute([':idImage'=>$idImage,':contenu'=>$image]);
    }
    
    //Update Question
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            UPDATE Question
            SET idImage = :idImage,
                texte = :texte,
                tempsQuestion = :temps,
                explication = :explication,
                niveau = :niveau,
                isVerified = 1
            WHERE idQuestion = :idQuestion
SQL);
    $stat->execute([':idQuestion'=>$idQuestion,
                    ':idImage'=>$idImage,
                    ':texte'=>$texteQ,
                    ':temps'=>$tempsQ,
                    ':explication'=>$explication,
                    ':niveau'=>$niveau]);
    
    //Ajout Réponse
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Reponse
            WHERE idQuestion = :id
SQL);
    $stat->execute([':id'=>$idQuestion]);

    $stat = MyPDO::getInstance()->prepare(<<<SQL
            SELECT MAX(idReponse) as "idReponse"
            FROM Reponse
SQL);
    $stat->execute();
    $idReponse = $stat->fetch()['idReponse'];
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO Reponse VALUES (:idR,:idQ,:bonneRep,:texteRep)
SQL);
    $i=0;
    foreach($reponses as $k => $v){
        $i++;
        $stat->execute([':idR'=>$idReponse+$i,':idQ'=>$idQuestion,':bonneRep'=>1,':texteRep'=>htmlspecialchars($v)]);
    }
    
    http_response_code(302);
    header("Location: moderation.php");
    die();

}
else
    die("Il manque des paramètres");