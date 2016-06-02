<?

class Auth {
    const REPLY_TO_EMAIL = '';
    // Token lifetime in seconds (2678400 is one month)
    const TOKEN_LIFETIME = 2678400;

    static public function getHost() {
        return $_SERVER['HTTP_HOST'];
    }

    static public function getAuthLink($token) {
        return $_SERVER['REQUEST_SCHEME'] . '://' . self::getHost() . $_ENV['BASE_URL'] . 'dashboard?token=' . $token;
    }

    static public function getId() {
        return $_ENV['JWT_ID'];
    }

    static public function isAuthorized() {
        $token = self::extractToken();
        return (bool) ($token && self::validateToken($token));
    }

	static public function generateToken(Subscriber $subscriber) {
        $time = time();
        $key = self::getId();
        $token = array(
            "iss" => self::getHost(),
            "aud" => self::getHost(),
            // Issued-at time. Contains the UTC Unix time at which this token was issued.
            "iat" => $time,
            // Expiration time. It contains the UTC Unix time after which you should no longer accept this token
            "exp" => $time + self::TOKEN_LIFETIME
        );

        // HS256 is the same as sha256
        return \Firebase\JWT\JWT::encode($token, $key, 'HS256');
	}

	static public function extractToken() {
	    $token = $_REQUEST['token'];
	    if (!$token) {
            // JWT token is passed via authorization header and looks like the following:
            // Authorization: Bearer <token>
            $headers = getallheaders();
            $token = preg_replace('/^Bearer\s+/i', '', $headers['Authorization']);
	    }
	    return $token;
	}

	static public function validateToken($string) {
	    $key = self::getId();
	    try {
            $decoded = \Firebase\JWT\JWT::decode($string, $key, array('HS256'));
        // TODO This way token parser message is lost
        } catch (Exception $e) {
            $decoded = false;
        }

        /*
         NOTE: This will now be an object instead of an associative array. To get
         an associative array, you will need to cast it as such:
        */
        return $decoded;
	}

	static public function emailToken($email, $token) {
	    // TODO Remove
	    $email = 'anna.shishlyakova@gmail.com';

        $link = self::getAuthLink($token);
	    $subject = 'Ссылка для входа в рассылку';
	    $message = sprintf('<p>Привет!</p><p>Вот ваша <a href="%1$s">ссылка для входа в личный кабинет</a> рассылки Сергея Короля.</p>', $link);

	    $headers = sprintf('From: %1$s <%2$s>'."\r\n"
        .'Reply-To: %1$s <%2$s>'."\r\n"
        .'Content-type: text/html; charset=utf-8'."\r\n"
        , $_ENV['EMAIL_SENDER_NAME'], $_ENV['REPLY_TO_EMAIL']);

        // TODO Use PHPMailer to send emails via smtp
        mail($email, $subject, $message, $headers);
	}
}

?>
