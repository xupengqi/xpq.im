<?php
class View {
    protected $context;

    public function __construct($c) {
        $this->context = $c;
    }

    public function render() {
        if(!empty($this->context->view)) {
            require_once '/view/'.$this->context->layout.'.php';
        }
        else {
            require_once '/view/error.php';
        }
    }

    public function renderModule($module, $return = false) {
        $config = $this->context->getConfig();
        $module = 'error';
        if(isset($this->context->modules[$module])) {
            $module = $this->context->modules[$module];
        }

        $viewFile = '/view/module/'.$viewId.'.php';
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].$config['appPath'].$viewFile)) {
            $viewFile = '/view/module/error.php';
        }
        
        if($return) {
            ob_start();
            require $viewFile;
            return ob_get_clean();
        }
        else {
            require $viewFile;
        }
    }

    private function js($file, $external = false) {
        $config = $this->context->getConfig();
        if($external) {
            return '<script src="'.$file.'" type="text/javascript"></script>';
        }
        else {
            return '<script src="'.$config['appPath'].'js/'.$file.'.js" type="text/javascript"></script>';
        }
    }

    private function css($file, $external = false) {
        $config = $this->context->getConfig();
        if($external) {
            return '<link href="'.$file.'" type="text/css" rel="stylesheet">';
        }
        else {
            return '<link href="'.$config['appPath'].'css/'.$file.'.css"  type="text/css" rel="stylesheet">';
        }
    }
}
