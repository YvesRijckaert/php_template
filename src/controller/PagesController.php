<?php

require_once __DIR__ . '/Controller.php'; //haal de functionaliteit op van de Controller

class PagesController extends Controller { //geef PagesController dezelfde macht als Controller

    public function index() { //de index pagina
        $this->set('title', 'Home'); //de title zetten we op home
        $this->set('currentPage', 'home'); //de currentPage = home
    }

    public function about() { //de about pagina
        $this->set('title', 'About'); //title zetten we op about
        $this->set('currentPage', 'about'); //de currentPage = about
    }

}