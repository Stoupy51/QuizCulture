<?php declare(strict_types=1);

/**
 * Classe permettant de retourner une instance unique de PDO
 * afin de ne pas multiplier les connexions au serveur de base de données.
 */
final class MyPDO
{
    /**
     * Instance unique de PDO.
     */
    private static ?PDO $PDOInstance = null;

    /**
     *  DSN pour la connexion BD.
     */
    private static string $DSN = '';

    /**
     * nom d'utilisateur pour la connexion BD.
     */
    private static string $username = '';

    /**
     * mot de passe pour la connexion BD.
     */
    private static string $password = '';

    /**
     * options du pilote BD.
     */
    private static array $driverOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    /**
     * Constructeur privé
     * (juste pour vous passer l'envie d'instancier des objets MyPDO).
     */
    private function __construct()
    {
    }

    /**
     * Point d'accès à l'instance unique.
     * L'instance est créée au premier appel
     * et réutilisée aux appels suivants.
     *
     * @throws Exception si la configuration n'a pas été effectuée
     *
     * @return PDO instance unique
     */
    public static function getInstance(): PDO
    {
        if (is_null(self::$PDOInstance)) {
            if (!self::hasConfiguration()) {
                throw new Exception(__CLASS__.': Configuration not set');
            }
            self::$PDOInstance = new PDO(self::$DSN, self::$username, self::$password, self::$driverOptions);
        }

        return self::$PDOInstance;
    }

    /**
     * Fixer la configuration de la connexion à la BD.
     *
     * @param string $dsn           DSN pour la connexion BD
     * @param string $username      utilisateur pour la connexion BD
     * @param string $password      mot de passe pour la connexion BD
     * @param array  $driverOptions options du pilote BD
     */
    public static function setConfiguration(
        string $dsn,
        string $username = '',
        string $password = '',
        array $driverOptions = []
    ): void {
        self::$DSN = $dsn;
        self::$username = $username;
        self::$password = $password;
        self::$driverOptions = $driverOptions + self::$driverOptions;
    }

    /**
     * Vérifier si la configuration de la connexion à la BD a été effectuée.
     */
    private static function hasConfiguration(): bool
    {
        return '' !== self::$DSN;
    }
}

/* Exemple de configuration et d'utilisation
require_once 'MyPDO.template.php';

MyPDO::setConfiguration('mysql:host=mysql;dbname=cutron01_music;charset=utf8', 'web', 'web');

$stat = MyPDO::getInstance()->prepare(<<<SQL
    SELECT id, name
    FROM artist
    ORDER BY name
SQL
);

$stat->execute();

while (($ligne = $stat->fetch()) !== false) {
    echo "<p>{$ligne['name']}\n";
}
*/
