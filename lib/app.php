<?php
class App {
    private $context;

    public function __construct() {
        $this->context = new Context();
    }

    public function Dispatch() {
        $requestParts = explode('?', $_SERVER['REQUEST_URI']);
        $config = $this->context->getConfig();
        $url = substr($requestParts[0], strlen($config['appPath']));
        $path = explode('/', $url);
        
        $curIndex = $this->processController($path);
        if ($curIndex < 0) {
            return;
        }
        
        $curIndex = $this->processAction($path, $curIndex);
        if ($curIndex < 0) {
            return;
        }
        
        $this->processRequestVars($path, $curIndex);

        // start controller and render
        $this->context->controller->render();
    }

    private function processController($path) {
        $config = $this->context->getConfig();

        if(empty($path[0]) || $path[0] == 'index.php') {
            $this->context->setController('index');
            return 0;
        }
        else if(file_exists(getcwd()."/".$config['controllerPath']."/{$path[0]}.php")) {
            $this->context->setController($path[0]);
            return 1;
        }
        else {
            $this->context->setController('Error');
            $this->context->setParam('error', 'Requested page '.$config['appPath'].'<b>'.$path[0].'</b> not found.');
            return -1;
        }
    }

    private function processAction($path, $actionIndex) {
        $config = $this->context->getConfig();
        $actionName = null;
        
        if (method_exists($this->context->controllerClass, 'index')) {
            $actionName = 'index';
        }
        
        if(count($path) > $actionIndex && !empty($path[$actionIndex]) && method_exists($this->context->controllerClass, $path[$actionIndex])) {
            $actionName = $path[$actionIndex];
            $actionIndex++;
        }
        

        if($actionName != null) {
            $this->context->setAction($actionName);
            return $actionIndex;
        }
        else {
            $this->context->setController('Error');
            $this->context->setParam('error', 'Requested action <b>'.$actionName.'</b> or default action not found in page '.$config['appPath'].$this->context->controllerName.'.');
            return -1;
        }
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
