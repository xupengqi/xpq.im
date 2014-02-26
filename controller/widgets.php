<?php
class IndexController extends Controller {
    public function index($var) {
        $this->context->setView('widgets');
        $this->context->setModule('head', 'head');
        $this->context->setModule('nav', 'nav');
        $this->context->setParam('navButtonClass', array('resume'=> '', 'git'=> '', 'widgets'=> 'active'));
    }
}