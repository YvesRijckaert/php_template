<?php

class Controller {

  public $route; //de route is publiek beschikbaar
  protected $viewVars = array(); //viewVars is enkel beschikbaar bij overerving
  protected $env = 'development';

  public function filter() { //filter functie
    if (basename(dirname(dirname(__FILE__))) != 'src') {
      $this->env = 'production';
    }
    call_user_func(array($this, $this->route['action'])); //dit laat ons toe om functies te gaan oproepen die we zelf geschreven hebben
    //vb. action =>index
  }

  public function render() { //render functie
    $this->set('js', '<script src="http://localhost:8080/js/script.js"></script>');
    $this->set('css', '');
    if($this->env == 'production') {
      $this->set('js', '<script src="js/script.js"></script>');
      $this->set('css', '<link rel="stylesheet" href="css/style.css">');
    }
    $this->createViewVarWithContent(); //maak eerst dit aan
    $this->renderInLayout(); //doe dan dit
  }

  public function set($variableName, $value) {
    $this->viewVars[$variableName] = $value;
  }

  private function createViewVarWithContent() {
    extract($this->viewVars, EXTR_OVERWRITE);
    ob_start(); //output buffer (als je gaat outputten ga je even bijhouden in een buffer)
    require __DIR__ . '/../view/' . strtolower($this->route['controller']) . '/' . $this->route['action'] . '.php'; //controller name gebruiken en naar kleine letters omzetten en we halen het mapje pages op en we plakken er action aan
    $content = ob_get_clean(); //al hetgeen wat in de buffer zit, steek je in de variabele content
    $this->set('content', $content); //dan gaan we die content nog eens gaan setten in een nieuwe variabele content dat je gebruikt in layout.php
  }

  private function renderInLayout() {
    extract($this->viewVars, EXTR_OVERWRITE); //alle viewvars gaan extracten
    include __DIR__ . '/../view/layout.php'; //layout gaan includen
  }

}
