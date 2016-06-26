<?

define('DEBUG', true);
error_reporting(E_ALL);

ini_set('display_errors', DEBUG ? 'On' : 'Off');

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('getallheaders')) {
    function getallheaders() {
       $headers = '';
       foreach ($_SERVER as $name => $value) {
           if (substr($name, 0, 5) == 'HTTP_') {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
}

date_default_timezone_set('Europe/Moscow');

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
// After load, all of the defined variables will be accessible with the getenv method and in the $_ENV and $_SERVER super-globals.
$dotenv->load();

spl_autoload_register(function ($class_name) {
    include __DIR__ . '/classes/' . $class_name . '.php';
});

Router::main();

?>