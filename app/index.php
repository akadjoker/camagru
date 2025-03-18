<?php
 
session_start();

 
define('ROOT', dirname(__FILE__));
define('CONTROLLERS', ROOT . '/controllers/');
define('MODELS', ROOT . '/models/');
define('VIEWS', ROOT . '/views/');
define('CONFIG', ROOT . '/config/');

require_once CONFIG . 'database.php';
require_once CONTROLLERS . 'BaseController.php';
require_once MODELS . 'BaseModel.php';
require_once MODELS . 'User.php';
require_once MODELS . 'Image.php';
require_once MODELS . 'Overlay.php';
// require_once ROOT . '/helpers/Mailer.php';   para gmail

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

 
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = CONTROLLERS . $controllerName . '.php';

 
if (file_exists($controllerFile)) 
{
    require_once $controllerFile;
    
    $controller = new $controllerName();
    
    if (method_exists($controller, $action)) 
    {
        $controller->$action();
    } else 
    {
        header('HTTP/1.0 404 Not Found');
        echo "Página não encontrada!  :(";
    }
} else 
{
    header('HTTP/1.0 404 Not Found');
    echo "Página não encontrada! :( Ups!";
}