<?

class Router {
	public static function main() {
		$response = null;

		try {
			$response = self::getResponse();
		} catch (NotAuthorizedException $e) {
			header('HTTP/1.1 401 Unauthorized', true, 401);
		} catch (Exception $e) {
			$response['error'] = $e->getMessage();
		}

		if ($response) {
			if (self::acceptsJson()) {
                header("Content-Type: application/json");
                echo json_encode($response);
            } else {
                header("Content-Type: text/html");
                if ($response['data']) {
                    echo $response['data'];
                } else if ($response['error']) {
                    echo $response['error'];
                }
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
                        throw new Exception('Нужно указать email.');
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
                        'issues' => Issue::getAll()
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
		return (bool) preg_match('/application\/json/', $headers['Accept']);
	}
}

class NotAuthorizedException extends Exception {}

?>