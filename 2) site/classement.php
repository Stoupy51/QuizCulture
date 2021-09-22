<?php declare(strict_types=1);
require_once "autoload.php";

$authentication = new UserAuthentication();

$page = new WebPage("Classement");
$page->appendToHead(<<<HTML
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/classement.css" rel="stylesheet">
HTML);

//Get Joueur
$user = $authentication->getUserFromSession();
$pseudo = $user->getPseudo();
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT points
    FROM Joueur
    WHERE pseudo = :pseudo
SQL);
$stat->execute([":pseudo"=>$pseudo]);
$points = $stat->fetch()["points"];

//Joueurs
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT pseudo, points
    FROM Joueur
    ORDER BY points DESC, idJoueur
SQL);
$stat->execute();
$joueurs = $stat->fetchAll();
$maPlace = 0;
foreach($joueurs as $k => $v) {
    $maPlace++;
    if ($v["pseudo"] == $pseudo)
        break;
}
$pasApparu = false;
$numero = 0;
$i = 1;
$classement1 = "";
foreach($joueurs as $k => $v){
    $numero++;
    $class1 = "nn1";
    $class2 = "impairN";
    if ($numero % 2 == 0) {
        $class1 = "nn2";
        $class2 = "pairN";
    }
    $moi = "";
    if ($numero == $maPlace) {
        $pasApparu = true;
        $i--;
        $moi = "gold";
    }
    $classement1 .= <<<HTML
                        <div class="d-flex flex-row justify-content-around py-2 px-4">
                            <div class="$class1 text-center p-1 px-3 $moi">{$numero}</div>
                            <div class="$class2 text-center w-50 p-1 $moi">{$v["pseudo"]}</div>
                            <div class="$class2 text-center w-25 p-1 $moi">{$v["points"]}</div>
                        </div>
HTML;
    if ($i >= 9) {
        if ($pasApparu == false)
            $classement1 .= <<<HTML
                        <div class="d-flex flex-row justify-content-around py-2 px-4">
                            <div class="ln1 text-center p-1 px-3 gold">{$maPlace}</div>
                            <div class="impairL text-center w-50 p-1 gold">{$pseudo}</div>
                            <div class="impairL text-center w-25 p-1 gold">{$points}</div>
                        </div>
HTML;
        break;
    }
    $i++;
}

//Get Ligue
$maxligue = [299,229,179,129,99,74,49,29,14];
$minligue = [230,180,130,100,75,50,30,15,-10];
$nomLigue = ["OR III","OR II","OR I","ARGENT III","ARGENT II","ARGENT I","BRONZE III","BRONZE II","BRONZE I"];
$ligueActuelle = "";

foreach ($maxligue as $k => $v) {
    if ($points <= $v){
        $ligueActuelle = $nomLigue[$k];
        $key = $k;
    }
    elseif ($points >= 300) {
        $ligueActuelle = "MASTER";
    }
}
//Joueurs de la ligue
if ($ligueActuelle == "MASTER") {
    $statligue = MyPDO::getInstance()->prepare(<<<SQL
        SELECT pseudo, points
        FROM Joueur
        WHERE points >= 300
        ORDER BY points DESC, idJoueur
SQL);
    $statligue->execute();
}
else {
    $statligue = MyPDO::getInstance()->prepare(<<<SQL
        SELECT pseudo, points
        FROM Joueur
        WHERE points BETWEEN :min AND :max
        ORDER BY points DESC, idJoueur
SQL);
    $statligue->execute([":min"=>$minligue[$key],":max"=>$maxligue[$key]]);
}
$joueursLigue = $statligue->fetchAll();
$maPlace = 0;
foreach($joueursLigue as $k => $v) {
    $maPlace++;
    if ($v["pseudo"] == $pseudo)
        break;
}
$pasApparu = false;
$numeroLigue = 0;
$i = 1;
$classement2 = "";
foreach($joueursLigue as $k => $v){
    $numeroLigue++;
    $class1 = "ln1";
    $class2 = "impairL";
    if ($numeroLigue % 2 == 0) {
        $class1 = "ln2";
        $class2 = "pairL";
    }
    $moi = "";
    if ($numeroLigue == $maPlace) {
        $pasApparu = true;
        $i--;
        $moi = "gold";
    }
    $classement2 .= <<<HTML
                        <div class="d-flex flex-row justify-content-around py-2 px-4">
                            <div class="$class1 text-center p-1 px-3 $moi">{$numeroLigue}</div>
                            <div class="$class2 text-center w-50 p-1 $moi">{$v["pseudo"]}</div>
                            <div class="$class2 text-center w-25 p-1 $moi">{$v["points"]}</div>
                        </div>
HTML;
    if ($i >= 9) {
        if ($pasApparu == false)
            $classement2 .= <<<HTML
                        <div class="d-flex flex-row justify-content-around py-2 px-4">
                            <div class="ln1 text-center p-1 px-3 gold">{$maPlace}</div>
                            <div class="impairL text-center w-50 p-1 gold">{$pseudo}</div>
                            <div class="impairL text-center w-25 p-1 gold">{$points}</div>
                        </div>
HTML;
        break;
    }
    $i++;
}
                        
$page->appendContent(<<<HTML
        <div class="d-flex flex-column">
            <h1 class="button mt-3 mx-5 p-2 text-center d-flex flex-column justify-content-between border">Pataïtaï</h1>
            <div class="d-flex flex-column flex-sm-row flex-grow-1 justify-content-around px-5">
                <div class="d-flex flex-column w-25">
                    <div class="button mx-auto mt-5 d-flex flex-column border w-100">
                        <div class="test m-2 connect text-center"><h3>Classement général</h3></div>
                    </div>
                    <div class="d-flex barre justify-content-between flex-row mx-auto mt-5 border w-75">
                        <div class="gold m-2 text-center">RANG</div>
                        <div class="gold m-2 text-center">PSEUDO</div>
                        <div class="gold m-2 text-center">POINTS</div>
                    </div>
                    <div class="d-flex flex-column">
$classement1
                    </div>
                </div>
                <div class="d-flex flex-column w-25">
                    <div class="button mx-auto mt-5 d-flex flex-column border w-100">
                        <div class="test m-2 connect text-center"><h3>Classement de ma ligue</h3></div>
                    </div>
                    <div class="d-flex flex-row mx-auto mt-1 d-flex w-75">
                        <p class="m-auto white ligue">LIGUE : </p>
                        <p class="m-auto gold ligue">$ligueActuelle</p>
                        <img class="m-auto ligueImg" src="img/$ligueActuelle.png" width="50px" height="50px">
                    </div>
                    <div class="d-flex barre justify-content-between flex-row mx-auto mt-1 d-flex border w-75">
                        <div class="gold m-2 text-center">RANG</div>
                        <div class="gold m-2 text-center">PSEUDO</div>
                        <div class="gold m-2 text-center">POINTS</div>
                    </div>
                    <div class="d-flex flex-column">
$classement2
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <a class="m-5 p-2 buttonquitter" href="."><svg width="26" height="27" viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.8 16.875V13.5H12.1333V10.125H20.8V6.75L26 11.8125L20.8 16.875ZM19.0667 15.1875V21.9375H10.4V27L0 21.9375V0H19.0667V8.4375H17.3333V1.6875H3.46667L10.4 5.0625V20.25H17.3333V15.1875H19.0667Z" fill="white"/>
                    </svg>
                    Quitter    
                </a>
            </div>
        </div>
HTML);

echo $page->toHTML();