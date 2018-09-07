<?php

require_once __DIR__ . '/Controller.php'; //laad controller file in
require_once __DIR__ . '/../dao/ItemDAO.php'; //laad itemDAO in

class ItemsController extends Controller { //geef ItemsController dezelfde macht als Controller

    private $itemDAO;

    function __construct() {
        $this->itemDAO = new ItemDAO(); //ItemDAO activeren
    }

    public function items() { //de functie loadItems
        $items = $this->itemDAO->selectAll(); //voer selectAll uit in itemDAO
        $this->set('items', $items); //alle items die je terugkreeg
        $this->set('title', 'Items'); //de title van de pagina is Items
        $this->set('currentPage', 'items'); //de currentPage is items
    }

}