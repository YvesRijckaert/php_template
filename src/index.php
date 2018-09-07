<?php
session_start(); //sessions aanzetten
ini_set('display_errors', true); //toon alle errors
error_reporting(E_ALL); //report alle errors in de browser

$routes = array( //alle pagina's
  'home' => array( //Home pagina
    'controller' => 'Pages', //heeft de controller Pages
    'action' => 'index' //met fucntie index
  ),
  'about' => array( //About pagina
    'controller' => 'Pages', //heeft de controller Pages
    'action' => 'about' //met functie about
  ),
  'items' => array( //Items pagina
    'controller' => 'Items', //heeft de controller Items
    'action' => 'items' //met functie loadItems
  ),
  'detail' => array( //Detail pagina
    'controller' => 'Images', //heeft de controller Images
    'action' => 'view' //met functie view
  ),
  'add' => array( //Add pagina
    'controller' => 'Images', //heeft de controller Images
    'action' => 'add' //met functie add
  ),
  'login' => array( //Login pagina
    'controller' => 'Users', //heeft de controller Users
    'action' => 'login' //met functie login
  ),
  'logout' => array( //Logout pagina
    'controller' => 'Users', //heeft de controller Users
    'action' => 'logout' //met functie logout
  ),
  'register' => array( //Register pagina
    'controller' => 'Users', //heeft de controller Users
    'action' => 'register' //met functie register
  )
);

//eerste check = checken of we we wel een pagina hebben opgevraagt
if(empty($_GET['page'])) { //is er een parameter page meegegeven? Als deze leeg is dan:
  $_GET['page'] = 'home'; //stel je de parameter in op home, zo verwijs je dan automatisch naar de home pagina
}

//tweede check = kijken of die pagina wel bestaat in de routes array dat opgevraagt is
if(empty($routes[$_GET['page']])) {
  header('Location: index.php'); //zo niet dan gaat hij automatisch naar de index pagina
  exit();
}

$route = $routes[$_GET['page']]; //de routes steken we nu in een aparte variabele route
$controllerName = $route['controller'] . 'Controller'; //daaraan plakken we nu de controller aan

require_once __DIR__ . '/controller/' . $controllerName . ".php"; //haal nu die controller op van de pagina da je wilt laden en plak er .php aan

$controllerObj = new $controllerName(); //nieuwe controller name aanmaken
$controllerObj->route = $route; //route variabele gaan instellen
$controllerObj->filter(); //dan roepen we de filter functie op (in Controller.php)
$controllerObj->render(); //dan roepen we de render functie op (in Controller.php)
