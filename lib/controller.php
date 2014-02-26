<?php
class Controller {
    protected $context;

    public function __construct($c) {
        $this->context = $c;
    }

    public function render() {
        // call controller/action
        $action = $this->context->action;
        $this->$action();

        // render view
        $view = new View($this->context);
        $view->render();
    }
}