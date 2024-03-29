<?php

$config = new PrestaShop\CodingStandards\CsFixer\Config();

$config
    ->setUsingCache(true)
    ->getFinder()
    ->in(__DIR__)
    ->exclude(['vendor', 'node_modules', '202', 'test', 'logs']);

return $config;