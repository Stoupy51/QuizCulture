<?php declare(strict_types=1);

/**
 * Classe associée à la gestion de la session
 */
class Session
{

    /**
     * Démarrer une session
     *
     * @return void
     *
     * @throws SessionException si la session ne peut être démarrée
     * @throws RuntimeException si le résultat de session_status() est incohérent
     *
     * @see session_status() https://www.php.net/manual/fr/function.session-status.php
     * @see headers_sent($file, $line) https://www.php.net/manual/fr/function.headers-sent
     * @see session_start() https://www.php.net/manual/fr/function.session-start.php
     */
    static public function start()
    {
        switch (session_status()) {
            case PHP_SESSION_DISABLED : // Session désactivée...
                throw new SessionException("Session disabled on this server!");
                // break ;

            case PHP_SESSION_NONE : // La session n'est pas démarrée, essayer de le faire
                // Si les en-têtes ont déjà été envoyés, c'est trop tard...
                if (headers_sent($file, $line))
                    throw new SessionException("HTTP headers are already sent in {$file} at line {$line}, so it is impossible to start a session, as the session cookie could not be sent back in time, Marty!");
                // Démarrer la session
                session_start();
                break;

            case PHP_SESSION_ACTIVE : // La session est déjà démarrée
                break;

            default : // Cas inconnu...
                throw new RuntimeException("Error on session management");
                // break ;
        }
    }
}

