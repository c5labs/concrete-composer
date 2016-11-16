<?php
/*
 * ----------------------------------------------------------------------------
 * Set our own version of __DIR__ as $__DIR__ so we can include this file on
 * PHP < 5.3 and have it not die wholesale.
 * ----------------------------------------------------------------------------
 */
$__DIR__ = 'vendor/concrete5/concrete5';

/* ----------------------------------------------------------------------------
 * Override some of the concrete5 dcore directory location
 * ----------------------------------------------------------------------------
 */
defined('DIRNAME_CORE') or define('DIRNAME_CORE', $__DIR__.'/concrete');

/*
 * ----------------------------------------------------------------------------
 * Add the vendor path to the list of include paths
 * ----------------------------------------------------------------------------
 */
ini_set('include_path', __DIR__.DIRECTORY_SEPARATOR.'vendor' . PATH_SEPARATOR . get_include_path());

/*
 * ----------------------------------------------------------------------------
 * Include all autoloaders.
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/application/bootstrap/autoload.php';

/*
 * ----------------------------------------------------------------------------
 * Dispatch the CMS.
 * ----------------------------------------------------------------------------
 */
$cms = require 'vendor/concrete5/concrete5/concrete/dispatcher.php';