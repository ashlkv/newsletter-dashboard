<?

class Url {
    static public function getHost() {
        return $_SERVER['HTTP_HOST'];
    }

    static public function getProtocol() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    }

    static public function getBaseLink() {
        return self::getProtocol() . self::getHost() . $_ENV['BASE_URL'];
    }
}

?>