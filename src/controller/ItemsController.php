<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../dao/ItemDAO.php';

class ItemsController extends Controller {

    private $itemDAO;

    function __construct() {
        $this->itemDAO = new ItemDAO();
    }

    public function items() {
        $items = $this->itemDAO->selectAll();
        $this->set('items', $items);
        $this->set('title', 'Items');
        $this->set('currentPage', 'items');
    }

}
