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
            // A downside to sending errors inside a status code header is that error text should be in English and cannot contain markup.
            // UTF-8 encoded cyrillic texts get messed up or fail to send entirely.
            // Sending errors in 'error' response property.
            if (self::acceptsJson()) {
                echo json_encode($response);
            } else {
                echo ($response['error'] ? $response['error'] : $response['data']);
            }
		}
	}

	public static function getResponse() {
		$parameters = explode('/', $_REQUEST['path']);
        $entity = count($parameters) > 0 ? $parameters[0] : null;
        $identifier = count($parameters) > 1 ? $parameters[1] : null;
        $property = count($parameters) > 2 ? $parameters[2] : null;

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
                        throw new Exception('Нужно указать email.');
                    }

                    $subscriber = new Subscriber($identifier);

                    // If user is subscribed, email authentication token.
                    if ($subscriber->isSubscribed()) {
                        $token = Auth::generateToken($subscriber);

                        Auth::emailToken($subscriber->email, $token);
                        $data = 'Ссылка для входа отправлена на почту.';
                    } else {
                        $error = sprintf('Вы ещё не подписаны на рассылку. <a href="%1$s">Подписаться</a>', Url::getBaseLink());
                    }
					break;

                // Single issue token
				case 'issue':
				    if (Issue::isAuthorized($identifier) && $property && $property === 'content') {
                        $data = Issue::getContent($identifier);
				    } else {
				        throw new NotAuthorizedException();
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
                        'issues' => Issue::getAll(),
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
		$acceptHeader = isset($headers['Accept']) ? $headers['Accept'] : (isset($headers['accept']) ? $headers['accept'] : null);
		return (bool) preg_match('/application\/json/', $acceptHeader);
	}
}

class NotAuthorizedException extends Exception {}

?>