<?php
class RepoController extends Controller {
    public function index() {
        $this->context->setParam('nav.button.git.class', 'active');
    }
}