<?php

$config = require 'config/lotgd.config.php';

if (file_exists('config/development.config.php'))
{
    $config = Zend\Stdlib\ArrayUtils::merge($config, require 'config/development.config.php');
}

return $config;
