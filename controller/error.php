<?php
class ErrorController extends Controller {
    public function index() {
        header("HTTP/1.0 404 Not Found");
    }
}
