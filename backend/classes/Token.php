<?

class Token {
	// Token lifetime in seconds (2678400 is one month)
    const TOKEN_LIFETIME = 2678400;

    static public function getId() {
        return $_ENV['JWT_ID'];
    }

	static public function generate($payload = array()) {
        $time = time();
        $key = self::getId();
        $token = array(
            "iss" => Url::getHost(),
            "aud" => Url::getHost(),
            // Issued-at time. Contains the UTC Unix time at which this token was issued.
            "iat" => $time,
            // Expiration time. It contains the UTC Unix time after which you should no longer accept this token
            "exp" => $time + self::TOKEN_LIFETIME
        );

        $token = array_merge($token, $payload);

        // HS256 is the same as sha256
        return \Firebase\JWT\JWT::encode($token, $key, 'HS256');
    }

    static public function extract() {
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
        if (!$token) {
            // JWT token is passed via authorization header and looks like the following:
            // Authorization: Bearer <token>
            $headers = getallheaders();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization'])) ? $headers['authorization'] : null;
            $token = preg_replace('/^Bearer\s+/i', '', $authHeader);
        }
        return $token;
    }

    static public function validate($string) {
        $key = self::getId();
        try {
            $decoded = \Firebase\JWT\JWT::decode($string, $key, array('HS256'));
        // TODO This way token parser message is lost
        } catch (Exception $e) {
            $decoded = false;
        }
        return $decoded;
    }
}

?>
