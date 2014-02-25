<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$vendorDir = __DIR__ . '/../../../../..';
$kernelDir = __DIR__ . '/../../../..';

if (file_exists($file = $vendorDir . '/autoload.php')) {
    $loader = require_once $file;
} else if (file_exists($file = $kernelDir . '/vendor/autoload.php')) {
    $loader = require_once $file;
} else if (file_exists($file = './vendor/autoload.php')) {
    $loader = require_once $file;
} else {
    throw new \RuntimeException('Not found composer autoload.');
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));