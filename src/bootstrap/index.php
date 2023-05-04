<?php

use mmaurice\PaymentLinksWizard\Wizard;

if (!defined('IN_PHAR')) {
    define('IN_PHAR', strlen(Phar::running()) > 0 ? true : false);
}

if (!defined('PHAR_NAMESPACE')) {
    define('PHAR_NAMESPACE', 'mmaurice\\PaymentLinksWizard');
}

if (!defined('PHAR_ROOT')) {
    define('PHAR_ROOT', dirname(dirname(__FILE__)));
}

if (!defined('ROOT')) {
    define('ROOT', str_replace('phar://', '', PHAR_ROOT));
}

$loader = require_once PHAR_ROOT . '/vendor/autoload.php';

if (IN_PHAR) {
    $loader->addPsr4(PHAR_NAMESPACE . '\\', PHAR_ROOT);
}

$wizard = new Wizard;
$wizard->run();