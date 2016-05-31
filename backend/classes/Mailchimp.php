<?

class MailchimpCredentials extends HttpCredentials {
    function __construct() {
        $this->username = $_ENV['MAILCHIMP_USERNAME'];
        $this->password = $_ENV['MAILCHIMP_API_KEY'];
    }
}

class Mailchimp {
	const API_BASE_URL = 'https://us12.api.mailchimp.com/3.0/';

	public static function getCampaigns() {
		$response = HttpRequest::get(
            self::API_BASE_URL . "campaigns",
            array(
                'sort_field' => 'send_time',
                'sort_dir' => 'DESC'
            ),
			new MailchimpCredentials()
        );
        return $response['data']['campaigns'];
	}

	public static function getCampaign($id) {
        // Sanitizing the id
	    $id = preg_replace('/[^0-9a-z]/i', '', $id);

        $response = HttpRequest::get(
            self::API_BASE_URL . "campaigns/" . $id,
            null,
            new MailchimpCredentials()
        );
        return $response['data'];
	}

	public static function getCampaignContent($id) {
        // Sanitizing the id
	    $id = preg_replace('/[^0-9a-z]/i', '', $id);

        $response = HttpRequest::get(
            self::API_BASE_URL . "campaigns/" . $id . "/content",
            null,
            new MailchimpCredentials()
        );
        return $response['data']['html'];
	}
}

?>