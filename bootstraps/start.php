<?php
/**
 * Custom Bootstrap.
 * 
 * This file bootstraps the CMS for use as a composer dependancy.
 *
 * @author   Oliver Green <oliver@c5dev.com>
 * @license  See attached license file
 *
 * @link https://c5dev.com
 */

/*
 * ----------------------------------------------------------------------------
 * Set our own version of __DIR__ as $__DIR__ so we can include this file on
 * PHP < 5.3 and have it not die wholesale.
 * ----------------------------------------------------------------------------
 */
$__DIR__ = '../vendor/concrete5/concrete5';

/*
 * ----------------------------------------------------------------------------
 * Override some of the concrete5 dcore directory location
 * ----------------------------------------------------------------------------
 */
defined('DIRNAME_CORE') or define('DIRNAME_CORE', $__DIR__.'/concrete');
defined('DIR_BASE') or define('DIR_BASE', dirname(dirname($_SERVER['SCRIPT_FILENAME'])));

/*
 * ----------------------------------------------------------------------------
 * Add the vendor path to the list of include paths
 * ----------------------------------------------------------------------------
 */
ini_set('include_path', DIR_BASE.DIRECTORY_SEPARATOR.'vendor' . PATH_SEPARATOR . get_include_path());

/*
 * ----------------------------------------------------------------------------
 * Require the composer autoloaders
 * ----------------------------------------------------------------------------
 */
require __DIR__.'/../vendor/autoload.php';

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
