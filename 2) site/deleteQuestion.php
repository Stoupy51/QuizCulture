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
    SELECT idImage
    FROM Question
    WHERE idQuestion = :id;

    DELETE FROM Reponse
    WHERE idQuestion = :id;

    DELETE FROM Question
    WHERE idQuestion = :id;
SQL);
$stat->execute([":id"=>$idQuestion]);
$idImage = $stat->fetch();

if ($idImage != 0) {
    $stat = MyPDO::getInstance()->prepare(<<<SQL
        DELETE FROM Image
        WHERE idImage = :id;
SQL);
    try {$stat->execute([":id"=>$idImage]);}
    catch (Exception $e) {};
}

http_response_code(302);
header("Location: /.");
die();