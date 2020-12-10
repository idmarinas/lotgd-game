<?php

return [
    'input_filter_specs' => [
        //-- Added in version 4.3.0
        'Lotgd\Core\Form\Filter\Cronjob'  => include 'data/form/core/grotto/cronjob/filter.php',
        'Lotgd\Core\Form\Filter\HomeSkin' => include 'data/form/core/grotto/home/filter.php',
        'Lotgd\Core\Form\Filter\Petition' => include 'data/form/core/petition/filter.php',
    ],
];
