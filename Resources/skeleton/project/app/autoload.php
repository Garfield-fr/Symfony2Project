<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    {{ registerNamespaces }}
));
$loader->registerPrefixes(array(
    {{ registerPrefixes }}
));
$loader->registerPrefixFallbacks(array(
    __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs',
));
$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));
$loader->register();

AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

{{ swiftmailer_autoload }}