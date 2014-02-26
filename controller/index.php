<?php
class IndexController extends Controller {
    public function index() {
        $this->context->setView('index');
        $this->context->setParam('navButtonClass', array('resume'=> 'active', 'git'=> '', 'widgets'=> ''));
    }
}