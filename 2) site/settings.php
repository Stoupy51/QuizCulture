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

$user = $authentication->getUserFromSession();
$message = "";

if (isset($_POST["oldPassword"])) {
    $stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idJoueur
    FROM Joueur
    WHERE motDePasse = :pass
    AND pseudo = :pseudo
SQL);
    $stat->execute([":pass"=>hash('sha512',$_POST["oldPassword"]),":pseudo"=>$user->getPseudo()]);
    $id = $stat->fetch();
    if (isset($id['idJoueur'])) {
        $id = $id['idJoueur'];
    }
    if ($id == false) {
        //Afficher un message "Ancien mot de passe incorrect"
        $message = <<<HTML
                    <div class="button mx-5 mt-5 d-flex flex-column">
                        <a class="blanc m-2 button px-5 text-center">Ancien mot de passe incorrect</a>
                    </div>
HTML;
    }
    else {
        /**
         * Permet de changer le pseudo
         */
        if (isset($_POST["newLogin"])) {
            $stat = MyPDO::getInstance()->prepare(<<<SQL
                SELECT pseudo
                FROM Joueur
                WHERE pseudo = :pseudo
SQL);
            $stat->execute([":pseudo"=>$_POST["newLogin"]]);
            if ($stat->fetch() == false) {
                $stat = MyPDO::getInstance()->prepare(<<<SQL
                    UPDATE Joueur SET pseudo = :pseudo
                    WHERE idJoueur = :id
SQL);
                $stat->execute([":pseudo"=>htmlspecialchars($_POST["newLogin"]),":id"=>$id]);
                $authentication->logout();
            }
        }

        /**
         * Permet de changer le mot de passe
         */
        if (isset($_POST["newPassword"])) {
            $stat = MyPDO::getInstance()->prepare(<<<SQL
                UPDATE Joueur SET motDePasse = :mdp
                WHERE idJoueur = :id
SQL);
                $stat->execute([":mdp"=>hash('sha512',$_POST["newPassword"]),":id"=>$id]);
                $authentication->logout();
        }
    }
}
else {
    //Afficher un message "Ancien mot de passe nécessaire"
    $message = <<<HTML
                    <div class="button mx-5 mt-5 d-flex flex-column">
                        <a class="blanc m-2 button px-5 text-center">Ancien mot de passe nécessaire</a>
                    </div>
HTML;
}








$page = new WebPage("Accueil");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/settings.css" rel="stylesheet">
HTML);

$page->appendContent(<<<HTML
        <div class="d-flex flex-column h-100 w-50 flex-grow-1 m-auto">
            <h1 class="button mt-3 mx-5 p-2 text-center d-flex flex-column justify-content-between border">Pataïtaï</h1>
            <div class="mx-5 mt-2 d-flex flex-column align-self-center">
                <p class="connect twrap text-center text-wrap">Paramètres</span>
            </div>
            <form name="connection" method="POST" action="settings.php">
                <div class="d-flex flex-column flex-wrap m-3 justify-content-center align-items-center">
                    <div class="button mt-5 d-flex flex-column">
                        <label class="blanc m-2 button px-5 text-center">
                            <input name="newLogin" type="text" placeholder="Nouveau pseudo" required>
                        </label>
                    </div>
                    <div class="button mx-5 mt-2 d-flex flex-column">
                        <label class="blanc m-2 button px-5 text-center">
                            <input name="newPassword" type="password" placeholder="Nouveau mot de passe" required>
                        </label>
                    </div>
                    <div class="button mx-5 mt-5 d-flex flex-column">
                        <label class="blanc m-2 button px-5 text-center">
                            <input name="oldPassword" type="password" placeholder="Ancien mot de passe" required>
                        </label>
                    </div>
                    <div class="d-flex flex-column m-5">
                        <button class="buttonvalider m-2 p-1" type="submit">Valider</button>
                        <a class="m-2 p-1 buttonquitter" href="."><svg width="26" height="27" viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.8 16.875V13.5H12.1333V10.125H20.8V6.75L26 11.8125L20.8 16.875ZM19.0667 15.1875V21.9375H10.4V27L0 21.9375V0H19.0667V8.4375H17.3333V1.6875H3.46667L10.4 5.0625V20.25H17.3333V15.1875H19.0667Z" fill="white"/>
                            </svg>
                            Quitter    
                        </a>
                    </div>
$message
                </div>               
            </form>
        </div>
HTML);

echo $page->toHTML();
