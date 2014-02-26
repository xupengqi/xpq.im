<?php
class WidgetsController extends Controller {
    public function index() {
        $this->context->setView('widgets');
        $this->context->setParam('navButtonClass', array('resume'=> '', 'git'=> '', 'widgets'=> 'active'));
    }
}