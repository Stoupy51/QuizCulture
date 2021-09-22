<?php declare(strict_types=1);

class WebPage {
    private string $head;
    private string $title;
    private string $body;

    /**
     * WebPage constructor.
     * @param string $title
     */
    public function __construct(string $title = "") {
        $this->title = $title;
        $this->head = "";
        $this->body = "";
    }

    /**
     * Accesseur de Body
     * @return string
     */
    public function getBody() : string {
        return $this->body;
    }

    /**
     * Accesseur de Head
     * @return string
     */
    public function getHead() : string {
        return $this->head;
    }

    /**
     * Accesseur de Head
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * Modificateur de Title
     * @param string $title
     */
    public function setTitle(string $title) {
        $this->title = $title;
    }

    /**
     * Ajouter un contenu dans Head
     * @param string $content
     */
    public function appendToHead(string $content) {
        $this->head .= $content;
    }

    /**
     * Ajouter un css dans Head
     * @param string $css
     */
    public function appendCss(string $css) {
        $this->head .= <<<HTML
        <style>
$css
        {}</style>
HTML;
    }

    /**
     * Ajouter un url css dans Head
     * @param string $url
     */
    public function appendCssUrl(string $url) {
        $this->head .= <<<HTML
        <link href="$url" rel="stylesheet" type="text/css"/>
HTML."\n";
    }

    /**
     * Ajouter un js dans Head
     * @param string $js
     */
    public function appendJs(string $js) {
        $this->head .= <<<HTML
        <script>
$js
        </script>
HTML."\n";
    }

    /**
     * Ajouter un url js dans Head
     * @param string $url
     */
    public function appendJsUrl(string $url) {
        $this->head .= <<<HTML
        <script src="$url" type="text/javascript"></script>
HTML."\n";
    }

    /**
     * Ajouter du contenu dans le body
     * @param string $content
     */
    public function appendContent(string $content) {
        $this->body .= <<<HTML
$content
HTML;
    }

    /**
     * Produire la page Web complète
     * @return string
     */
    public function toHTML() : string {
        return <<<HTML
<!doctype html>
<html lang="fr">
    <head>
    <title>{$this->title}</title>
{$this->head}
    </head>
    <body class="h-100 w-100">
{$this->body}
    </body>
</html>
HTML;
    }

    /**
     * Donne la date et l'heure de la dernière modification du script principal.
     * @return string
     */
    public static function getLastModification() : string {
        return strftime("%c",getlastmod());
    }

    /**
     * Protége les caractères spéciaux pouvant dégrader la page Web
     * @param string $string
     * @return string
     */
    public static function escapeString(string $string) : string {
        setlocale(LC_ALL, 'fr_FR.UTF-8');
        return htmlspecialchars($string, ENT_QUOTES|ENT_HTML5);
    }
}