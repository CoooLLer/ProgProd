<?php

use App\Core\Kernel;
use Composer\Autoload\ClassLoader;

require_once 'db_cfg.php';

require_once 'config.php';

require_once 'routes.php';

require_once 'vendor/autoload.php';

$loader = new ClassLoader();
$loader->add('App', __DIR__ . '/App');
$loader->register();
$loader->setUseIncludePath(true);

$kernel = Kernel::getInstance();
$kernel->init($config, $routes);
$kernel->run();