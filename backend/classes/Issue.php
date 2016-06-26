<?

class Issue {
	// Token lifetime in seconds (31536000 is one year)
    const TOKEN_LIFETIME = 630720000;

	public static function getAll() {
		$campaigns = Mailchimp::getCampaigns();
        // Returning an array of objects
        $issues = array();

        foreach ($campaigns as $campaign) {
            $issues[] = self::extractIssue($campaign);
        }

        return $issues;
	}

	public static function get($id) {
		$meta = Mailchimp::getCampaign($id);
		return $meta;
	}

	public static function getContent($id) {
		$content = Mailchimp::getCampaignContent($id);
		$meta = self::get($id);
		$title = $meta['settings']['subject_line'];

		// FIXME This regexp is non-robust.
		$content = preg_replace('/<div style="text-align: center; font-size: 12px; line-height: 1.4; font-family: \'Helvetica Neue\', Helvetica, sans-serif;">.*\*\|EMAIL\|\*.*<\/div>/Us', '', $content);
		$content = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>' . $title . '</title></head>' . $content . '</html>';
		return $content;
	}

	private static function extractIssue($campaign) {
		$id = $campaign['id'];
		$datetime = new DateTime($campaign['send_time']);
		$timestamp = $campaign['send_time'] ? $datetime->getTimestamp() : null;

		$title = $campaign['settings']['title'];
		$fullSubject = $campaign['settings']['subject_line'];
		preg_match('/— (.*)/i', $fullSubject, $subjectMatches);
		$subject = $subjectMatches[1];

		preg_match('/#(\d+)/i', $title, $numberMatches);
		$number = count($numberMatches) && count($numberMatches) > 1 ? intval($numberMatches[1]) : 0;

		$link = self::extractLink($campaign);

		$authToken = Token::extract();
		$issueToken = self::generateToken($id);

		return array(
			'id' => $id,
           'timestamp' => $timestamp,
           'title' => $title,
           'subject' => $subject,
           'number' => $number,
           'link' => $link . '?token=' . $authToken,
           'shareUrl' => $link . '?token=' . $issueToken
       );
	}

	private static function extractLink($campaign) {
	    $link = Url::getBaseLink() . 'backend/issue/' . $campaign['id'] . '/content';
	    return $link;
	}

    static public function validateToken($string, $issueId) {
        $decoded = Token::validate($string);

        // If there is an issue id encoded in token payload, check token issue id against issue id parameter.
        // If there is no issue id encoded in token payload, consider token invalid.
        return $decoded && $decoded->issueId ? $decoded->issueId === $issueId : false;
    }

    static public function generateToken($issueId) {
        $payload = array(
            'issueId' => $issueId,
            'exp' => time() + self::TOKEN_LIFETIME
        );
        return Token::generate($payload);
    }

    static public function isAuthorized($issueId) {
        $token = Token::extract();
        return (bool) ($token && self::validateToken($token, $issueId));
    }

}

?>