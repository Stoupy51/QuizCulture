<?php declare(strict_types=1);

class Joueur {
    private int     $idJoueur;
    private string  $pseudo;
    private string  $motDePasse;
    private int     $points;
    private int     $role;

    /**
     * Cette méthode permet de récupérer les infos de la base de données
     * et de les transformer en objet de la classe Joueur
     * @param int $idJoueur
     * @return Joueur
     * @throws Exception
     */
    public static function createFromId(int $idJoueur) : self {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM joueur
    WHERE idJoueur = :id
SQL);
        $stat->execute([':id'=>$idJoueur]);
        $stat->setFetchMode(PDO::FETCH_CLASS,Joueur::class);
        if (($joueur = $stat->fetch()) !== false)
            return $joueur;
        else
            throw new Exception("Le joueur ne peut pas être trouvé dans la base de données");
    }

    /**
     * Accesseur sur le pseudo du Joueur.
     * @return string
     */
    public function getPseudo() : string {
        return $this->pseudo;
    }

    /**
     * Accesseur sur les points du Joueur
     * @return int
     */
    public function getPoints() : int {
        return $this->points;
    }

    /**
     * Accesseur sur le rôle du Joueur (0 ou null = Joueur, 1 = modérateur)
     * @return int
     */
    public function getRole() : int {
        return $this->role;
    }

    /**
     * Accesseur sur l'ID du Joueur
     * @return int
     */
    public function getId() : int {
        return $this->idJoueur;
    }
}