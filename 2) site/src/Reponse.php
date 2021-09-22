<?php declare(strict_types=1);

class Reponse {
    private int     $idReponse;
    private int     $idQuestion;
    private string  $texteReponse;
    private bool    $bonneReponse;


    /**
     * Cette méthode permet de récupérer les infos de la base de données
     * et de les transformer en objet de la classe Reponse
     * @param int $idReponse
     * @return Reponse
     * @throws Exception
     */
    public static function createFromId(int $idReponse) : self {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM reponse
    WHERE idReponse = :id
SQL);
        $stat->execute([':id'=>$idReponse]);
        $stat->setFetchMode(PDO::FETCH_CLASS,Reponse::class);
        if (($reponse = $stat->fetch()) !== false)
            return $reponse;
        else
            throw new Exception("La réponse ne peut pas être trouvée dans la base de données");
    }

    /**
     * Accesseur sur le texte à afficher de la réponse.
     * @return string
     * @throws Exception
     */
    public function getTexte(): string {
        return $this->texteReponse;
    }
}