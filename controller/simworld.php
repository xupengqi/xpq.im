<?php
class SimWorldController extends Controller {
    public function index() {
        $this->context->setParam('nav.button.simworld.class', 'active');
    }
}