<?php
class SimWorld_RoadController extends Controller {
    public function index() {
        $this->context->setParam('nav.button.simworld.class', 'active');
    }
}