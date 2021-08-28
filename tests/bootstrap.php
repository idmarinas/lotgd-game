<?php

use Symfony\Component\Dotenv\Dotenv;

require \dirname(__DIR__).'/vendor/autoload.php';

//-- Include constants
require_once \dirname(__DIR__).'/src/constants.php';

if (file_exists(\dirname(__DIR__).'/config/bootstrap.php'))
{
    require \dirname(__DIR__).'/config/bootstrap.php';
}
elseif (method_exists(Dotenv::class, 'bootEnv'))
{
    (new Dotenv())->bootEnv(\dirname(__DIR__).'/.env');
}

//-- Init Debugger
\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT, __DIR__.'/../storage/log/tracy');
