<?php
// File : public/index.php

require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Session.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/Router.php';

Session::start();

$router = new Router();

require_once ROOT_PATH . '/config/routes.php';

$router->dispatch();