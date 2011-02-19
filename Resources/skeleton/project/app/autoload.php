<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    {{ registerNamespaces }}
));
$loader->registerPrefixes(array(
    {{ registerPrefixes }}
));
$loader->register();
