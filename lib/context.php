<?php
class Context {
    public $config;
    public $controller;
    public $controllerName;
    public $controllerClass;
    public $action;
    public $view;
    public $modules = array();
    public $request = array();
    public $helpers = array();
    public $models = array();

    public function __construct() {
        $this->config = new Config();
        if($this->config->config['debug']) {
            ini_set('display_errors', 1);
            ini_set('html_errors', 1);
        }
    }

    public function getConfig() {
        return $this->config->config;
    }

    public function setController($ctrl) {
        require_once $this->config->config['controllerPath']."$ctrl.php";
        $this->controllerName = $ctrl;
        $this->controllerClass = $ctrl.'Controller';
        $this->controller  = new $this->controllerClass($this);
    }
    public function getControllerName() {
        return $this->controllerName;
    }

    public function setAction($a) {
        $this->action = $a;
    }

    public function setView($view) {
        $this->view = $view;
    }
    public function setModule($key, $val) {
        $this->modules[$key] = $val;
    }

    public function setParam($key, $val) {
        $this->request[$key] = $val;

        if($key == 'token') {
            $this->loadModels(array('user'));
            $this->loadHelpers(array('response'));
            $this->user = $this->models['user']->getSingle(array('token'=>$val), false);
        }
    }

    public function getParam($key) {
        return $this->request[$key];
    }
}