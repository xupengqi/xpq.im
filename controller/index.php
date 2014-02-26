<?php
class IndexController extends Controller {
    public function index() {
        $this->context->setView('index');
        $this->context->setModule('head', 'head');
        $this->context->setModule('nav', 'nav');
        $this->context->setParam('navButtonClass', array('resume'=> 'active', 'git'=> '', 'widgets'=> ''));
    }
}