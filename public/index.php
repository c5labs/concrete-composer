<?php

/**
 * Dispatcher.
 * 
 * This file replaces the concrete/dispatcher.php to bootstrap the CMS for 
 * use as a composer dependancy. 
 * 
 * We need to serve static concrete5 files from below the root, so we proxy 
 * the requests to specfic files or boot the CMS depending on the request.
 *
 * @author   Oliver Green <oliver@c5dev.com>
 * @license  See attached license file
 *
 * @link https://c5dev.com
 */
require_once __DIR__.'/../bootstraps/proxy.php';

/*
 * ----------------------------------------------------------------------------
 *  Initialize the proxy class with the $_SERVER vars.
 * ----------------------------------------------------------------------------
 */

$proxy = new ConcreteCoreProxy($_SERVER);

/*
 * ----------------------------------------------------------------------------
 * If the proxy doesn't handle the request, we boot the CMS via our custom 
 * bootstrap which configures it to run as a composer dependency.
 * ----------------------------------------------------------------------------
 */

if (!$proxy->handle()) {
    require_once __DIR__.'/../bootstraps/start.php';
}
