<?php

require 'vendor/autoload.php';

$loader = new Composer\Autoload\ClassLoader();
$prefixesPsr4 = $loader->getPrefixesPsr4();

echo "<pre>";
print_r($prefixesPsr4);
echo "</pre>";