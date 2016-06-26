<?

class Auth {
    // Token lifetime in seconds (2678400 is one month)
    const TOKEN_LIFETIME = 2678400;

    static public function getAuthLink($token) {
        return Url::getBaseLink() . 'dashboard?token=' . $token;
    }

    static public function isAuthorized() {
        $token = Token::extract();
        return (bool) ($token && self::validateToken($token));
    }

    static public function generateToken(Subscriber $subscriber) {
        $payload = array(
            'email' => $subscriber->email,
            'exp' => time() + self::TOKEN_LIFETIME
        );
        return Token::generate($payload);
    }

    static public function validateToken($string) {
        $decoded = Token::validate($string);

        // FIXME Change to if ($decoded && !$decoded->email) in a month when all tokens without email expire
        if ($decoded && isset($decoded->issueId)) {
            $decoded = false;
        }
        return $decoded;
    }

	static public function emailToken($email, $token) {
        $link = self::getAuthLink($token);
	    $subject = 'Ссылка для входа в рассылку';
	    $message = sprintf('<p>Привет!</p><p>Вот ваша <a href="%1$s">ссылка для входа в личный кабинет</a> рассылки Сергея Короля.</p>', $link);

        self::email($email, $subject, $message);
	}

    // TODO Move to a separate utility
	static public function email($to, $subject, $message) {
        $from = sprintf('%1$s <%2$s>', $_ENV['EMAIL_SENDER_NAME'], $_ENV['SMTP_USER']);

	    // PHPMailer
	    if (class_exists('PHPMailer')) {
            $mail = new PHPMailer();

            // smtp, faster
            if ($_ENV['USE_SMTP']) {
                $mail->IsSMTP();
                $mail->Host = $_ENV['SMTP_SERVER'];
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USER'];
                $mail->Password = $_ENV['SMTP_PASSWORD'];
            // sendmail, slower
            } else {
                $mail->IsMail();
            }

            $mail->Debugoutput = 'html';
            $mail->CharSet = 'utf-8';
            $mail->From = $_ENV['SMTP_USER'];
            $mail->FromName = $_ENV['EMAIL_SENDER_NAME'];
            $mail->AddAddress($to);
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $result = $mail->Send();
            if (!$result) {
                throw new Exception('Не могу отправить письмо. Ошибка: ' . $mail->ErrorInfo);
            }
        // Regular built-in php mail function
	    } else {
	        $headers = sprintf('From: %1$s'."\r\n"
                    .'Reply-To: %1$s'."\r\n"
                    .'Content-type: text/html; charset=utf-8'."\r\n"
                    , $from);

            mail($to, $subject, $message, $headers);
	    }
	}
}

?>
