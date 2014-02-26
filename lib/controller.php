<?php
class Controller {
    protected $context;

    public function __construct($c) {
        $this->context = $c;
    }

    public function render() {
        // call controller/action
        $this->context->action();

        // render view
        $view = new View($this->context);
        $view->render();
    }
}