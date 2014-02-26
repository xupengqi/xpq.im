<?php
require_once '/lib/app.php';
require_once '/lib/config.php';
require_once '/lib/context.php';
require_once '/lib/view.php';
require_once '/lib/controller.php';

date_default_timezone_set('America/Los_Angeles');
session_start();

$app = new App();
$app->Dispatch();
