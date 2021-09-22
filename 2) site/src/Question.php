<?php declare(strict_types=1);

class Question {
    private int     $idQuestion;
    private int     $idImage;
    private int     $idJoueur;
    private string  $texte;
    private int     $tempsQuestion;
    private int     $typeReponse;
    private string  $explication;
    private int     $niveau;
    private bool    $isVerified;


    /**
     * Cette méthode permet de récupérer les infos de la base de données
     * et de les transformer en objet de la classe Question
     * @param int $idQuestion
     * @return Question
     * @throws Exception
     */
    public static function createFromId(int $idQuestion) : self {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM question
    WHERE idQuestion = :id
SQL);
        $stat->execute([':id'=>$idQuestion]);
        $stat->setFetchMode(PDO::FETCH_CLASS,Question::class);
        if (($question = $stat->fetch()) !== false)
            return $question;
        else
            throw new Exception("La question ne peut pas être trouvée dans la base de données");
    }

    /**
     * Cette méthode permet de retourner toutes les bonnes réponses à la question.
     * Cela est particulièrement utile lorsqu'on doit écrire la réponse mais
     * qu'il y a plusieurs façons de l'écrire.
     * @return array
     * @throws Exception
     */
    public function getBonnesReponses(): array {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM reponse
    WHERE idQuestion = :id
    AND bonneReponse = true
SQL);
        $stat->execute([':id'=>$this->idQuestion]);
        $stat->setFetchMode(PDO::FETCH_CLASS,Reponse::class);
        return $stat->fetchAll();
    }

    /**
     * Cette méthode permet de retourner toutes les réponses à la question.
     * Cela est particulièrement utile lorsqu'on doit écrire la réponse mais
     * qu'il y a plusieurs façons de l'écrire.
     * @return array
     * @throws Exception
     */
    public function getReponses(): array {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM reponse
    WHERE idQuestion = :id
SQL);
        $stat->execute([':id'=>$this->idQuestion]);
        $stat->setFetchMode(PDO::FETCH_CLASS,Reponse::class);
        return $stat->fetchAll();
    }

    /**
     * Cette méthode permet de retourner l'image liée à la question.
     * @return string
     * @throws Exception
     */
    public function getImage() : int {
        return $this->idImage;
    }
    
    /**
     * Cette méthode permet de retourner le type de la question.
     */
    public function getType() : int {
        return $this->typeReponse;
    }

    /**
     * Cette méthode permet de retourner le texte de la question.
     */
    public function getTexte() : string {
        return $this->texte;
    }

    public function getPseudo() : string {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
        SELECT pseudo
        FROM joueur
        WHERE idJoueur = :id
SQL);
            $stat->execute([':id'=>$this->idJoueur]);
            if (($joueur = $stat->fetch()) !== false)
                return $joueur["pseudo"];
            else
                throw new Exception("La question ne peut pas être trouvée dans la base de données");
    }

    /**
     * Cette méthode permet de retourner le niveau de la question.
     */
    public function getNiveau() : int {
        return $this->niveau;
    }

    /**
     * Cette méthode permet de retourner le temps de la question.
     */
    public function getTemps() : int {
        return $this->tempsQuestion;
    }

    /**
     * Cette méthode permet de retourner l'explication de la question.
     */
    public function getExplication() : string {
        return $this->explication;
    }
}