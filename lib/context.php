<?php
class Context {
    public $config = array(
            'debug' => true,
            'appDir' => '/xpq.im/',
            'appPath' => '/',
            'controllerPath' => '/controller/',
            'db'	=> array(
                    'host' => '127.0.0.1',
                    'user' => 'mobile1',
                    'pass' => '@mobile-development',
                    'name' => 'broadcaster')
    );
    public $controller;
    public $controllerName;
    public $controllerClass;
    public $action;
    public $view;
    public $modules = array();
    public $params = array();
    public $helpers = array();
    public $models = array();
    public $debug = array();

    public function __construct() {
        if($this->config['debug']) {
            ini_set('display_errors', 1);
            ini_set('html_errors', 1);
        }
    }

    public function setController($ctrl) {
        require_once $this->config['controllerPath']."$ctrl.php";
        $this->controllerName = $ctrl;
        $this->controllerClass = $ctrl.'Controller';
        $this->controller  = new $this->controllerClass($this);
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

    public function setParam($key, $val, $default = false) {
        if ($default && !isset($this->params[$key]) || !$default) {
            $this->params[$key] = $val;
        }
    }
    
    public function log_debug($key, $val) {
        $this->debug[$key] = $val;
    }
    public function log_error($key, $val) {
        $this->debug[$key] = $val;
        error_log("$key :: $val");
    }
}