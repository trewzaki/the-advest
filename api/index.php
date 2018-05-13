<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use MrMe\Web\Router as Router;
use Api\Controller\ExampleCommand as ExampleCommand;
use Api\Controller\Stock as Stock;

require_once "CONFIG.php";
$loader = require './vendor/autoload.php';

date_default_timezone_set("Asia/Bangkok");
define("DOMPDF_ENABLE_REMOTE", true);
ini_set('display_startup_errors', $_CONFIG['COMMON']['DEBUG']);
ini_set('display_errors', $_CONFIG['COMMON']['DEBUG']); // set to 0 when not debugging
error_reporting(E_ALL | ~E_NOTICE);

$router = new Router($_CONFIG);
$router->route("example/{F}", new ExampleCommand);
$router->route("stock/{F}", new Stock);

$router->start();
?>
