<?

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
// After load, all of the defined variables will be accessible with the getenv method and in the $_ENV and $_SERVER super-globals.
$dotenv->load();

spl_autoload_register(function ($class_name) {
    include __DIR__ . '/classes/' . $class_name . '.php';
});

$parameters = explode('/', $_REQUEST['path']);
$entity = $parameters[0];
$id = $parameters[1];
$property = $parameters[2];

$headers = getallheaders();
$acceptsJson = preg_match('/application\/json/', $headers['Accept']);

$data = null;

switch($entity) {
    case 'dashboard':
        $data = array(
            'issues' => Issue::getAll()
        );
        break;

    case 'issue':
        if ($property && $property === 'content') {
            $data = Issue::getContent($id);
        } else {
            $data = Issue::get($id);
        }
        break;

    case 'issues':
        $data = Issue::getAll();
        break;
}

if ($acceptsJson) {
    header("Content-Type: application/json");
    $response = array(
        'data' => $data,
        'error' => null
    );
    echo json_encode($response);
} else {
    header("Content-Type: text/html");
    echo $data;
}

?>