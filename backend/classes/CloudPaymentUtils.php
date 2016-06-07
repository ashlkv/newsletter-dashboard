<?

class CloudPaymentUtils {
	const SUBSCRIBERS_LIST_PATH = './../assets/subscribers.csv';

	const STATUS_ACTIVE = 'Работает';
	const STATUS_REJECTED = 'Отклонена';
	const STATUS_CANCELLED = 'Отменена';
	const STATUS_OVERDUE = 'Просрочена';
	const STATUS_FINISHED = 'Завершена';

	private static $subscribers;

	static function getSubscribers() {
        if (!isset(self::$subscribers)) {
            if (!file_exists(self::SUBSCRIBERS_LIST_PATH)) {
                throw new Exception('Unable to find a file containing list of subscribers.');
            }
            $content = file_get_contents(self::SUBSCRIBERS_LIST_PATH);
            $lines = explode("\n", $content);

            // Remove table heade included in csv
            array_splice($lines, 0, 1);

            self::$subscribers = array();
            foreach ($lines as $line) {
                $data = str_getcsv($line, "\t");
                $email = $data[6];
                self::$subscribers[$email] = array(
                    // TODO Convert date to timestamp
                    'subscribedAt' => $data[0],
                    'email' => $email,
                    'status' => $data[1]
                );
            }
        }
        return self::$subscribers;
    }
}

?>