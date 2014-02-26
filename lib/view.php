<?php
class View {
    protected $context;

    public function __construct($c) {
        $this->context = $c;
    }

    public function render() {
        if(!empty($this->context->view)) {
            require_once '/view/'.$this->context->view.'.php';
        }
        else {
            require_once '/view/error.php';
        }
    }

    public function renderModule($module, $return = false) {
        $config = $this->context->config;
        $moduleName = $module;
        if(isset($this->context->modules[$module])) {
            $moduleName = $this->context->modules[$module];
        }

        $moduleFile = '/view/module/'.$moduleName.'.php';
        $modulePath = $_SERVER['DOCUMENT_ROOT'].$config['appDir'].$moduleFile;
        if (!file_exists($modulePath)) {
            $this->context->log_error('Module file not found', $modulePath);
            $moduleFile = '/view/module/error.php';
        }

        if($return) {
            ob_start();
            require $moduleFile;
            return ob_get_clean();
        }
        else {
            require $moduleFile;
        }
    }

    private function js($file, $external = false) {
        $config = $this->context->config;
        if($external) {
            return '<script src="'.$file.'" type="text/javascript"></script>';
        }
        else {
            return '<script src="'.$config['appPath'].'js/'.$file.'.js" type="text/javascript"></script>';
        }
    }

    private function css($file, $external = false) {
        $config = $this->context->config;
        if($external) {
            return '<link href="'.$file.'" type="text/css" rel="stylesheet">';
        }
        else {
            return '<link href="'.$config['appPath'].'css/'.$file.'.css"  type="text/css" rel="stylesheet">';
        }
    }
}
