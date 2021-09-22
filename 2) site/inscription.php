<?php declare(strict_types=1);
require_once "src/WebPage.php";
require_once "src/MyPDO.php";

$page = new WebPage("Accueil");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/inscription.css" rel="stylesheet">
HTML);

$page->appendContent(<<<HTML
        <div class="d-flex flex-column h-100 flex-grow-1 w-50 m-auto">
            <h1 class="button mt-3 mx-5 p-2 text-center d-flex flex-column justify-content-between border">Pataïtaï</h1>
            <div class="mx-5 mt-2 d-flex flex-column align-self-center">
                <p class="connect twrap text-center text-wrap">Inscription</p>
            </div>
            <form name="connection" method="POST" action="inscription.php">
                <div class="d-flex flex-column flex-wrap m-3 justify-content-center align-items-center">
                    <div class="button mt-2 d-flex flex-column">
                        <label class="blanc m-2 button px-5 text-center">
                            <input name="pseudo" type="text" placeholder="Pseudo" required>
                        </label>
                    </div>
                    <div class="button mx-5 mt-2 d-flex flex-column">
                        <label class="blanc m-2 button px-5 text-center">
                            <input name="mdp" type="password" placeholder="Mot de passe" required>
                        </label>
                    </div>
                    <div class="button mx-5 mt-2 d-flex flex-column">
                        <label class="blanc m-2 button px-5 text-center">
                            <input name="mdpRep" type="password" placeholder="Répéter mot de passe" required>
                        </label>
                    </div>
                    <div class="button mx-5 mt-5 d-flex border border-dark">
                        <button class="buttonvalider" type="submit">Valider</button>
                    </div>
                    <div class="button mx-5 mt-2 d-flex border border-dark quitter">
                        <button class="buttonquitter" onclick="location.href='.';">Quitter</button>
                    </div>
                </div>               
            </form>
        </div>
HTML);

if (isset($_POST["pseudo"]) && isset($_POST["mdp"]) && isset($_POST["mdpRep"])) {
    $stat = MyPDO::getInstance()->prepare(<<<SQL
            SELECT MAX(idJoueur) as "id"
            FROM Joueur
SQL);
    $stat->execute();
    $NextId = $stat->fetch()['id']+1;
    $pseudo = htmlspecialchars($_POST["pseudo"]);
    $mdp = $_POST["mdp"];
    $mdpS = hash('sha512', $mdp);

    $stat = MyPDO::getInstance()->prepare(<<<SQL
            SELECT pseudo
            FROM Joueur
            WHERE pseudo = :pseudo
SQL);
    $stat->execute([':pseudo'=>$pseudo]);
    if ($stat->fetch() !== false) {
        $pseudo = false;
        $page->appendContent(<<<HTML
        <div class="mx-auto d-flex flex-column align-self-center">
                <p class="connect twrap text-center text-wrap">Le Pseudo est déjà utilisé</p>
        </div>
HTML);
    }



    if ($_POST["mdp"] == $_POST["mdpRep"] && $pseudo !== false) {
        $page->appendContent(<<<HTML
        <div class="mx-auto d-flex flex-column align-self-center">
                <p class="connect twrap text-center text-wrap">Inscription bien effectuée</p>
        </div>
HTML);
        $stat = MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO joueur VALUES(:idJoueur, :pseudo, :motDePasse, 0, 0)
SQL);
        $stat->execute([':idJoueur'=>$NextId, ':pseudo'=>$pseudo, ':motDePasse'=>$mdpS]);
    }
    else {
        $page->appendContent(<<<HTML
        <div class="mx-auto d-flex flex-column align-self-center">
                <p class="connect twrap text-center text-wrap">Le mot de passe ne correspond pas au mot de passe répété</p>
        </div>
HTML);
    }
}
echo $page->toHTML();
