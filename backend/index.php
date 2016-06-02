<?

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
// After load, all of the defined variables will be accessible with the getenv method and in the $_ENV and $_SERVER super-globals.
$dotenv->load();

spl_autoload_register(function ($class_name) {
    include __DIR__ . '/classes/' . $class_name . '.php';
});

Router::main();

?>