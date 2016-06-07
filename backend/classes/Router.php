<?

class Router {
	public static function main() {
		$response = null;

		try {
			$response = self::getResponse();
		} catch (NotAuthorizedException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized', true, 401);
		} catch (Exception $e) {
			$response['error'] = $e->getMessage();
		}

		if ($response) {
		    $contentType = self::acceptsJson() ? 'application/json' : 'text/html';
            header("Content-Type: " . $contentType . "; charset=utf-8");
            if ($response['error']) {
                // TODO A downside to sending errors inside a status code header is that error text should be in English.
                // TODO UTF-8 encoded cyrillic texts get messed up.
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 ' . $response['error'], true, 500);
            } else {
                echo (self::acceptsJson() ? json_encode($response) : $response['data']);
            }
		}
	}

	public static function getResponse() {
		$parameters = explode('/', $_REQUEST['path']);
        $entity = $parameters[0];
        $identifier = $parameters[1];
        $property = $parameters[2];

        try {
		    $authorized = Auth::isAuthorized();
        } catch (Exception $e) {
            throw new NotAuthorizedException($e->getMessage());
        }

		$data = null;
		$error = null;

        // Authentication request
        if ($entity === 'auth') {
            if ($authorized) {
                $data = 'OK';
            } else {
                throw new NotAuthorizedException();
            }
		// If not authorized and requesting token, generate and email token link
		} else if (!$authorized) {
			switch($entity) {
				case 'token':
					if (!$identifier) {
                        throw new Exception('You need to specify an email address.');
                    }

                    $subscriber = new Subscriber($identifier);

                    // If user is subscribed, email authentication token.
                    if ($subscriber->isSubscribed()) {
                        $token = Auth::generateToken($subscriber);

                        Auth::emailToken($subscriber->email, $token);
                        // TODO Approve this text
                        $data = 'Ссылка для входа отправлена на почту.';
                    } else {
                        // TODO Approve this text
                        $error = 'Вы ещё не подписаны на рассылку. <a href="http://sergeykorol.ru/blog/newsletter/">Подписаться</a>';
                    }
					break;

				default:
			        // Return 401 response code for all other non-authorized requests
		            throw new NotAuthorizedException();
					break;
			}
        // Authorized requests
        } else {
            switch($entity) {
                case 'dashboard':
                    $data = array(
                        'issues' => array_slice(Issue::getAll(), 0, 3)
                    );
                    break;

                case 'issue':
                    if ($property && $property === 'content') {
                        $data = Issue::getContent($identifier);
                    } else {
                        $data = Issue::get($identifier);
                    }
                    break;

                case 'issues':
                    $data = Issue::getAll();
                    break;

                default:
                    // TODO
                    break;
            }
        }

        return array(
            'data' => $data,
            'error' => $error
        );
	}

	public static function acceptsJson() {
		$headers = getallheaders();
		$acceptHeader = $headers['Accept'] ? $headers['Accept'] : $headers['accept'];
		return (bool) preg_match('/application\/json/', $acceptHeader);
	}
}

class NotAuthorizedException extends Exception {}

?>