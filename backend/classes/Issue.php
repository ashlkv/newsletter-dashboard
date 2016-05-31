<?

class Issue {
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
		$datetime = new DateTime($campaign['send_time']);
		$timestamp = $campaign['send_time'] ? $datetime->getTimestamp() : null;

		$title = $campaign['settings']['title'];
		$fullSubject = $campaign['settings']['subject_line'];
		preg_match('/— (.*)/i', $fullSubject, $subjectMatches);
		$subject = $subjectMatches[1];

		preg_match('/#(\d+)/i', $title, $numberMatches);
		$number = count($numberMatches) && count($numberMatches) > 1 ? intval($numberMatches[1]) : 0;

		$link = self::extractLink($campaign);

		return array(
			'id' => $campaign['id'],
           'timestamp' => $timestamp,
           'title' => $title,
           'subject' => $subject,
           'number' => $number,
           'link' => $link
       );
	}

	private static function extractLink($campaign) {
	    return '/backend/issue/' . $campaign['id'] . '/content';
	}
}

?>