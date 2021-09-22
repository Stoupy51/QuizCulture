<?php declare(strict_types=1);
require_once "autoload.php";

//Prendre les questions validées
$stmt = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idQuestion
    FROM Question
    WHERE isVerified = 1
SQL);
$stmt->execute();
$questions = $stmt->fetchAll();

//Prendre une question au hasard parmi les validées
$max = Count($questions)-1;
$idQuestion = $questions[random_int(0,$max)]["idQuestion"];
$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT pseudo, texte, idImage, niveau, tempsQuestion, explication, typeReponse
    FROM Question q, Joueur j
    WHERE q.idJoueur = j.idJoueur
    AND q.idQuestion = :id
SQL);
$stat->execute([":id"=>$idQuestion]);
$all = $stat->fetch();
$pseudo = htmlspecialchars($all["pseudo"]);
$texte = htmlspecialchars($all["texte"]);
$idImage = $all["idImage"];
$niveau = $all["niveau"];
$temps = $all["tempsQuestion"];
$explication = htmlspecialchars($all["explication"]);
$typeReponse = $all["typeReponse"];
$script = "reponse";
/**
 * Reponse à choisir
 */
if ($typeReponse == 1) {
    $script = "choix";
    $stat = MyPDO::getInstance()->prepare(<<<SQL
        SELECT bonneReponse, texteReponse, idReponse
        FROM Reponse
        WHERE idQuestion = :id
SQL);
    $stat->execute([":id"=>$idQuestion]);
    $all = $stat->fetchAll();
    $bonnesRep = "";
    foreach ($all as $k => $v) {
        if ($v["bonneReponse"] == 1)
            $bonnesRep .= htmlspecialchars($v["texteReponse"]);
    }
    $bonnesRep = base64_encode($bonnesRep);
    $tab = [htmlspecialchars($all[0]["texteReponse"]),htmlspecialchars($all[1]["texteReponse"]),htmlspecialchars($all[2]["texteReponse"]),htmlspecialchars($all[3]["texteReponse"])];
    shuffle($tab);
    $reponse1 = $tab[0];
    $reponse2 = $tab[1];
    $reponse3 = $tab[2];
    $reponse4 = $tab[3];

    $affichage = <<<HTML
        <div class="d-flex">
            <button class="button m-2 mt-5 reponse" id="reponse1">{$reponse1}</button>
            <button class="button m-2 mt-5 reponse" id="reponse2">{$reponse2}</button>
            <button class="button m-2 mt-5 reponse" id="reponse3">{$reponse3}</button>
            <button class="button m-2 mt-5 reponse" id="reponse4">{$reponse4}</button>
        </div>
HTML;
}

/**
 * Reponse à écrire
 */
else {
    $stat = MyPDO::getInstance()->prepare(<<<SQL
        SELECT texteReponse, idReponse
        FROM Reponse
        WHERE idQuestion = :id
SQL);
    $stat->execute([":id"=>$idQuestion]);
    $all = $stat->fetchAll();
    $bonnesRep = strtoupper($all[0]["texteReponse"]);
    for ($i=1; $i < Count($all); $i++) {
        $bonnesRep .= ", ".strtoupper($all[$i]["texteReponse"]);
    }
    $bonnesRep = base64_encode($bonnesRep);
    $affichage = <<<HTML
        <input type="hidden" class="button mt-1 bonnereponse" id="reponseB" value="">
        <div class="d-flex flex-column">
            <input class="button m-2 mt-3 reponse" id="reponse" onkeypress="return written(event)" value="" placeholder="Écrivez votre réponse">
        </div>
HTML;
}







$html = new WebPage("Question !");
$html->appendToHead(<<<HTML
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="css/normal.css" rel="stylesheet">
HTML);

$html->appendContent(<<<HTML
    <div class="d-flex flex-column w-75 m-auto align-items-center container">
        <div class="d-flex p-5 align-items-start">
            <div class="d-flex flex-column mx-2" id="buttonTemps">
                <div class="d-flex temps button px-3 py-1" id="temps">
                    <img src="img/horloge.png" width="25" height="25">
                    <div id="timer">$temps</div>
                </div>
                <div class="d-flex justify-content-center niveau button mt-2 px-3 py-1">
                    <img src="img/trophy.png" width="25" height="25">
                    <div>$niveau</div>
                </div>
            </div>
            <div class="question button text-center px-5 mx-2">$texte</div>
        </div>
        <img src="imageQuestion.php?id=$idImage" height="300" class="image button">
$affichage
        <div id="ExpComp" class="p-3 m-2"></div>
        <div class="d-flex mb-5">
            <a class="mx-5 p-2 buttonquitter" href="."><svg width="26" height="27" viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8 16.875V13.5H12.1333V10.125H20.8V6.75L26 11.8125L20.8 16.875ZM19.0667 15.1875V21.9375H10.4V27L0 21.9375V0H19.0667V8.4375H17.3333V1.6875H3.46667L10.4 5.0625V20.25H17.3333V15.1875H19.0667Z" fill="white"/>
                            </svg>
                            Quitter    
            </a>
            <a class="mx-5 p-2 buttonsuivant" href="normal.php"><svg width="25" height="25" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <rect width="45" height="45" fill="url(#pattern0)"/>
                <defs>
                <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                <use xlink:href="#image0" transform="scale(0.0166667)"/>
                </pattern>
                <image id="image0" width="60" height="60" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAABmJLR0QA/wD/AP+gvaeTAAABB0lEQVRoge3ZPW7CUBBF4RN6lCYlcsvSIqUKDUSCDVGQnZEf6GigiJEswIlt7DcvM/eTpuYeEAgBiIiIiIiIODAuL4QX4FDezHhLEp/AsXJvtnOGd7xxK9NFA7sV7Dq6Ltht9G/BLqP/CnYX3STYVXTTYDfRbYLvji6ADbDv8MCW1+nLSQF8ZDA+WfQmg9FJo3cZDE4abT20z3u9jHuoCfbiADzx8+ELwMhuiw3vwXMqr26db+zfe33coumz8p7B2GSxAFNgm8HoJLFnE2ANfGUQMHhsLtrGLm1m9idULASLhWCxECwWgsVCsFgIFgvXPzP96y8VTTwT7O9SgMfyRERERERE0joBUgJUrHNlP4YAAAAASUVORK5CYII="/>
                </defs>
                </svg>
                Suivant
            </a>
        </div>
    </div>
HTML);
$html->appendContent(<<<HTML
    <script>
        var bonnesReponses = "$bonnesRep";
        var expComplementaires = "$explication";
        var submitedBy = "(Question proposée par $pseudo)";
        var time = '$temps';
    </script>
    <script src="js/$script.js"></script>
HTML);

echo $html->toHTML();