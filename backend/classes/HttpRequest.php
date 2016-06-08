<?

class HttpRequest {
    public static function get($url, $params, HttpCredentials $credentials = null) {
        if ($params && is_array($params)) {
            $pairs = array();
            foreach ($params as $key => $value) {
                $pairs[] = urlencode($key) . "=" . urlencode($value);
            }
            $url .= "?" . join("&", $pairs);
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if ($credentials) {
            $username = $credentials->getUsername();
            $password = $credentials->getPassword();
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        }

        return self::handleResponse($curl);
    }

    private static function handleResponse($curl) {
        $plainResponse = curl_exec($curl);

        if (curl_errno($curl)) {
            $response = array(
                "status" => "error",
                "data" => "ENDPOINT_ERROR"
            );
        } else {
            $responseData = json_decode($plainResponse, true);

            $response = array(
                "status" => "",
                "data" => $responseData
            );

            $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $isSuccess = $httpStatusCode == 200 || $httpStatusCode == 201 || $httpStatusCode == 202;


            $response["status"] = $isSuccess ?
                "success" :
                "error";
        }

        curl_close($curl);

        return $response;
    }
}

?>