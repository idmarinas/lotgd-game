<?php

return [
    'input_filter_specs' => [
        //-- Added in version 4.1.0
        'Lotgd\Core\Form\Filter\Configuration' => include 'data/form/core/grotto/configuration/filter.php',
        //-- Added in version 4.3.0
        'Lotgd\Core\Form\Filter\Cronjob'  => include 'data/form/core/grotto/cronjob/filter.php',
        'Lotgd\Core\Form\Filter\HomeSkin' => include 'data/form/core/grotto/home/filter.php',
    ],
];
