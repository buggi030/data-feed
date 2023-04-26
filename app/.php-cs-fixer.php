<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests')
    ->in('bin')
    ->in('config')
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
])
    ->setFinder($finder)
    ;