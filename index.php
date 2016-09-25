<?php
require 'vendor/autoload.php';

/*
 * ----------------------------------------------------------------------------
 * Set our own version of __DIR__ as $__DIR__ so we can include this file on
 * PHP < 5.3 and have it not die wholesale.
 * ----------------------------------------------------------------------------
 */
$__DIR__ = 'vendor/concrete5/concrete5';

defined('DIRNAME_APPLICATION') or define('DIRNAME_APPLICATION', 'application');
defined('DIRNAME_UPDATES') or define('DIRNAME_UPDATES', 'updates');
defined('DIRNAME_CORE') or define('DIRNAME_CORE', $__DIR__.'/concrete');
//defined('DIR_BASE') or define('DIR_BASE', __DIR__);

/*
 * ----------------------------------------------------------------------------
 * Set required constants, including directory names, attempt to include site configuration file with database
 * information, attempt to determine if we ought to skip to an updated core, etc...
 * ----------------------------------------------------------------------------
 */
require $__DIR__ . '/concrete/bootstrap/configure.php';

/*
 * ----------------------------------------------------------------------------
 * Include all autoloaders.
 * ----------------------------------------------------------------------------
 */
require $__DIR__ . '/concrete/bootstrap/autoload.php';

/*
 * ----------------------------------------------------------------------------
 * Begin concrete5 startup.
 * ----------------------------------------------------------------------------
 */
/** @var \Concrete\Core\Application\Application $cms */
$cms = require $__DIR__ . '/concrete/bootstrap/start.php';

/*
 * ----------------------------------------------------------------------------
 * Run the runtime.
 * ----------------------------------------------------------------------------
 */
$runtime = $cms->getRuntime();
if ($response = $runtime->run()) {

    /*
     * ------------------------------------------------------------------------
     * Shut it down.
     * ------------------------------------------------------------------------
     */
    $cms->shutdown();
} else {
    return $cms;
}
