<?

class Subscriber {
	private $subscribed;

	function __construct($email) {
		$this->email = strtolower($email);
	}

	private function hydrate($data) {
		// Fetching user data from Mailchimp list
		$data = Mailchimp::getSubscriber($this->email);

		$this->subscribed = (bool) $data;

		if ($data) {
			$this->id = $data['id'];
			$this->uniqueEmailId = $data['unique_email_id'];
			$this->status = $data['status'];
			$this->timestamp = strtotime($data['timestamp_opt']);
			$this->firstName = $data['merge_fields']['FNAME'];
			$this->fullName = implode($data['merge_fields'], ' ');
		}
	}

	public function isSubscribed() {
		if (!isset($this->subscribed)) {
			$this->hydrate($data);
		}
		return $this->subscribed;
	}
}

?>