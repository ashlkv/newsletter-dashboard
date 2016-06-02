<?

class MailchimpCredentials extends HttpCredentials {
    function __construct() {
        $this->username = $_ENV['MAILCHIMP_USERNAME'];
        $this->password = $_ENV['MAILCHIMP_API_KEY'];
    }
}

class Mailchimp {
	const API_BASE_URL = 'https://us12.api.mailchimp.com/3.0/';
	const MAX_SUBSCRIBERS_COUNT = 10000;

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

	public static function getNewsletterList() {
	    // Fetch all lists
        $response = HttpRequest::get(
            self::API_BASE_URL . "lists/",
            null,
            new MailchimpCredentials()
        );

        $lists = $response['data']['lists'];
        $newsletterList = null;
        foreach ($lists as $list) {
            if ($list['name'] === 'Newsletter') {
                $newsletterList = $list;
                break;
            }
        }

        if (!$newsletterList) {
            throw new Exception('Не могу получить информацию о списках');
        }

        return $newsletterList;
	}

    /**
    * Fetches all subscribers (a list limited to MAX_SUBSCRIBERS_COUNT)
    */
	public static function getSubscribers() {
	    $newsletterList = self::getNewsletterList();

        $fields = array(
            'members.id',
            'members.email_address',
            'members.unique_email_id',
            'members.status',
            'members.merge_fields',
            'members.timestamp_opt'
        );
        // With newsletter list id, fetch subscribers
        $response = HttpRequest::get(
            self::API_BASE_URL . "lists/" . $newsletterList['id'] . "/members",
            array(
                'fields' => implode($fields, ','),
                'count' => self::MAX_SUBSCRIBERS_COUNT
            ),
            new MailchimpCredentials()
        );

        $response['data']['members'];
	}

	public static function getSubscriber($email) {
	    $newsletterList = self::getNewsletterList();
	    $subscriberHash = md5(strtolower($email));

	    // With newsletter list id, attemp to fetch a subscriber with given email
        $response = HttpRequest::get(
            self::API_BASE_URL . "lists/" . $newsletterList['id'] . "/members/" . $subscriberHash,
            null,
            new MailchimpCredentials()
        );

        $subscriber = null;
        // Email in the subsribers list, regardless of status
        if ($response['data']['status'] !== 404) {
            $subscriber = $response['data'];
        }
        return $subscriber;
	}
}

?>