<?php declare(strict_types=1);

class Image {
    private int     $idImage;
    private string  $contenu;

    /**
     * Cette méthode permet de récupérer les infos de la base de données
     * et de les transformer en objet de la classe Image
     * @param int $id
     * @return Image
     * @throws Exception
     */
    public static function createFromId(int $id) : self {
        $stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM image
    WHERE idImage = :id
SQL);
        $stat->execute([':id'=>$id]);
        $stat->setFetchMode(PDO::FETCH_CLASS,Image::class);
        if (($image = $stat->fetch()) !== false)
            return $image;
        else
            throw new Exception("L'image ne peut pas être trouvée dans la base de données");
    }


    /**
     * Accesseur sur le contenu de l'image
     * @return string
     * @throws Exception
     */
    public function getContenu(): string {
        return $this->contenu;
    }
}