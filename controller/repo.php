<?php
class RepoController extends Controller {
    public function index() {
        $this->context->setView('repo');
        $this->context->setParam('navButtonClass', array('resume'=> '', 'git'=> 'active', 'widgets'=> ''));
    }
}