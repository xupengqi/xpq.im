<?php
class ErrorController extends RESTController {
    public function index($params) {
        header("HTTP/1.0 404 Not Found");
        $this->context->setView('error');
        $this->context->setModule('head', 'head');
        $this->context->setModule('nav', 'nav');
    }
}
