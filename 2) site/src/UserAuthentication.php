<?php declare(strict_types=1);

class UserAuthentication
{
    public const LOGIN_INPUT_NAME = 'login';
    public const PASSWORD_INPUT_NAME = 'password';
    public const LOGOUT_INPUT_NAME = 'logout';
    public const SESSION_KEY = '__UserAuthentication__';
    public const SESSION_USER_KEY = 'user';

    private ?Joueur $user = null;

    /**
     * Constructeur
     *
     * @throws SessionException si la session ne peut pas être démarrée
     */
    public function __construct()
    {
        try {
            // Restauration depuis la session si possible
            $this->user = $this->getUserFromSession();
        } catch (Exception $exception) {
        }
    }

    /**
     * Production d'un formulaire de connexion
     * @param string $action URL cible du formulaire
     * @param string $submitText texte du bouton d'envoi
     *
     * @return string code HTML du formulaire
     */
    public function loginForm(string $action, string $submitText = 'OK'): string
    {
        $loginInputName = self::LOGIN_INPUT_NAME;
        $passwordInputName = self::PASSWORD_INPUT_NAME;
        // Le formulaire
        return <<<HTML
<form name='auth' action='{$action}' method='POST' autocomplete='off'>
  <div>
    <input type='text' name='{$loginInputName}' placeholder='login'>
    <input type='password' name='{$passwordInputName}' placeholder='pass' autocomplete='new-password'>
    <input type='submit'   value='{$submitText}'>
  </div>
</form>
HTML;
    }

    /**
     * Validation de la connexion de l'utilisateur
     *
     * @return User utilisateur authentifié
     *
     * @throws AuthenticationException si l'authentification échoue
     */
    public function getUserFromAuth(): Joueur
    {
        if (!isset($_POST[self::LOGIN_INPUT_NAME]) || !isset($_POST[self::PASSWORD_INPUT_NAME])) {
            throw new AuthenticationException("pas de login/pass fournis");
        }

        try {
            // Préparation de la requête
            $stmt = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Joueur
    WHERE pseudo = :login
    AND motDePasse = :password
SQL
            );
            $stmt->execute([
              ':login' => $_POST[self::LOGIN_INPUT_NAME],
              ':password' => hash('sha512',$_POST[self::PASSWORD_INPUT_NAME])
            ]);

            // Test de réussite de la sélection
            if (($user_data = $stmt->fetch()) !== false) {
                $user = Joueur::createFromId(intval($user_data['idJoueur']));
                $this->setUser($user);

                return $user;
            }
        } catch (PDOException $pdoException) {
            throw new AuthenticationException("Erreur base de données");
        } catch (SessionException $pdoException) {
            throw new AuthenticationException("Erreur de session");
        }

        throw new AuthenticationException("Login/pass incorrect");
    }

    /**
     * Affecte l'utilisateur passé en paramètre à la propriété $user et le mémoire dans les données de session
     * @param User $user utilisateur à affecter
     * @throws SessionException si la session ne peut pas être démarrée
     */
    protected function setUser(Joueur $user): void
    {
        $this->user = $user;
        Session::start();
        $_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY] = $this->user;
    }

    /**
     * Test si un utilisateur est mémorisé dans les données de session
     * @return bool un utilisateur est connecté
     * @throws SessionException si la session ne peut pas être démarrée
     */
    public function isUserConnected(): bool
    {
        Session::start();
        return isset($_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY])
          && $_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY] instanceof Joueur;
    }

    /**
     * Formulaire de déconnexion de l'utilisateur
     * @param string $action URL cible du formulaire
     * @param string $buttonText texte du bouton de déconnexion
     * @return string le formulaire
     */
    public function logoutForm(string $action, string $buttonText): string
    {
        // Convertir tous les caractères spéciaux dans $buttonText
        $buttonText = htmlspecialchars($buttonText, ENT_QUOTES, 'utf-8');
        // Proposer le formulaire de déconnexion
        $inputName = self::LOGOUT_INPUT_NAME;
        return <<<HTML
    <form action='$action' method='POST'>
    <input type='submit' value="$buttonText" name='{$inputName}'>
    </form>
HTML;
    }

    /**
     * Déconnecter l'utilisateur
     *
     * @return void
     * @throws SessionException si la session ne peut pas être démarrée
     */
    public function logout(): void
    {
        Session::start();
        unset($_SESSION[self::SESSION_KEY]);
        $this->user = null;
    }

    /**
     * Lecture de l'objet User dans la session
     *
     * @return User
     *
     * @throws SessionException si la session ne peut pas être démarrée
     * @throws Exception si l'objet n'est pas dans la session
     */
    public function getUserFromSession(): Joueur
    {
        // Mise en place de la session
        Session::start();
        // La variable de session existe ?
        if (isset($_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY])) {
            // Lecture de la variable de session
            $user = $_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY];
            // Est-ce un objet du bon type ?
            if ($user instanceof Joueur) {
                // OUI ! on le retourne
                $this->setUser($user);
                return $user;
            }
        }
        // NON ! exception NotInSessionException
        throw new Exception();
    }

    /**
     * Accesseur à l'utilisateur connecté
     *
     * @return User utilisateur connecté
     * @throws Exception Si aucun utilisateur n'est connecté
     */
    public function getUser(): Joueur
    {
        if (!isset($this->user)) {
            throw new Exception("Aucun utilisateur connecté");
        }

        return $this->user;
    }
}

