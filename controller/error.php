<?php
class ErrorController extends Controller {
    public function index($params) {
        header("HTTP/1.0 404 Not Found");
        $this->context->setView('error');
    }
}
