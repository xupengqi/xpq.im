<?php
class IndexController extends Controller {
    public function index() {
        $this->context->setParam('nav.button.resume.class', 'active');
    }
}