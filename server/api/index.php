<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require '../conf/database.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$getAll = FALSE;
$IdChamado = null;
foreach ($uri as $currentPath) {
  if ($currentPath === "chamados") {
    $getAll = TRUE;
    break;
  }
}

if (!$getAll) {
  $IdChamado = (int) basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
  if (!$IdChamado) {
    if ($requestMethod !== 'POST') {
      header("HTTP/1.1 404 Not Found");
      exit();
    }
  }
}

require "../Controller/ChamadoController.php";

$db = (new Database())->getConnection();

$chamadoController = new ChamadoController($db, $requestMethod, $IdChamado);
$chamadoController->processRequest();
