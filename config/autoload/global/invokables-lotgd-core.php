<?php

use Doctrine\DBAL\Tools\Console;
use Doctrine\ORM\Tools\Console\Command;

return [
    'service_manager' => [
        'invokables' => [
            //-- Added in version 4.2.0
            'Doctrine\ORM\Mapping\AnsiQuoteStrategy'        => 'Doctrine\ORM\Mapping\AnsiQuoteStrategy',
            'Doctrine\ORM\Mapping\UnderscoreNamingStrategy' => 'Doctrine\ORM\Mapping\UnderscoreNamingStrategy',
        ],
    ],
];
