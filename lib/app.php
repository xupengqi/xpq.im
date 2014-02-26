<?php
class App {
    private $context;

    public function __construct() {
        $this->context = new Context();
    }

    public function Dispatch() {
        $requestParts = explode('?', $_SERVER['REQUEST_URI']);
        $config = $this->context->config;
        $url = substr($requestParts[0], strlen($config['appPath']));
        $path = explode('/', $url);

        $curIndex = $this->processController($path);
        $curIndex = $this->processAction($path, $curIndex);

        $this->processRequestVars($path, $curIndex);

        // start controller and render
        $this->context->controller->render();
    }

    private function processController($path) {
        $config = $this->context->config;
        $ctrlPath = getcwd()."/{$config['controllerPath']}/{$path[0]}.php";

        if(empty($path[0])) {
            $this->context->setController('index');
            $this->context->log_debug('controller', 'index, default');
            return 0;
        }
        else if(file_exists($ctrlPath)) {
            $this->context->setController($path[0]);
            $this->context->log_debug('controller', 'index, default');
            return 1;
        }
        else {
            $this->context->setController('Error');
            $this->context->setParam('error', 'Requested page <b>'.$path[0].'</b> not found.');
            $this->context->log_debug('controller not found', $ctrlPath);
            return 0;
        }
    }

    private function processAction($path, $actionIndex) {
        $config = $this->context->config;
        $actionName = null;

        if (method_exists($this->context->controllerClass, 'index')) {
            $actionName = 'index';
        }

        if(count($path) > $actionIndex && !empty($path[$actionIndex]) && method_exists($this->context->controllerClass, $path[$actionIndex])) {
            $actionName = $path[$actionIndex];
            $actionIndex++;
        }

        $this->context->setAction($actionName);

        return $actionIndex;
    }

    private function processRequestVars($path, $varIndex) {
        for ($i=$varIndex; $i<count($path); $i++) {
            $this->context->setParam('var'.($i-$varIndex), $path[$i]);
        }

        foreach ($_REQUEST as $key=>$val) {
            $this->context->setParam($key, $val);
        }
    }
}
