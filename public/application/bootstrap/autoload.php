<?php
/*
 * ----------------------------------------------------------------------------
 * Load all composer autoload items.
 * ----------------------------------------------------------------------------
 */

// If the checker class is already provided, likely we have been included in a separate composer project
if (!class_exists(\DoctrineXml\Checker::class)) {
    // Otherwise, lets try to load composer ourselves
    if (!@include(realpath(__DIR__ . '/../../vendor/autoload.php'))) {
        echo 'Third party libraries not installed. Ensure that you have installed the dependencies by running the composer install command.';
        die(1);
    }
}
