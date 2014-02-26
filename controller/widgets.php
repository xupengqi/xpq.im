<?php
class WidgetsController extends Controller {
    public function index() {
        $this->context->setParam('nav.button.widgets.class', 'active');
    }
}