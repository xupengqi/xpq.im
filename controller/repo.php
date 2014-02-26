<?php
class IndexController extends Controller {
    public function index($var) {
        $this->context->setView('repo');
        $this->context->setModule('head', 'head');
        $this->context->setModule('nav', 'nav');
        $this->context->setParam('navButtonClass', array('resume'=> '', 'git'=> 'active', 'widgets'=> ''));
    }
}