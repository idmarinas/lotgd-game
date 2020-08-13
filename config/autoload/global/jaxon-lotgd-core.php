<?php

return [
    'jaxon' => [
        'core' => [
            'request' => [
                'uri' => 'jaxon.php',
            ],
            'process' => [
                'exit' => false,
            ],
        ],
        'dialogs' => [
            'default' => [
                'alert' => 'toastr',
                'modal' => 'semantic',
            ],
            'classes' => [
                'semantic' => \Lotgd\Core\Jaxon\Library\Semantic\Modal::class,
            ],
            'toastr' => [
                'options' => [
                    'progressBar'       => true,
                    'newestOnTop'       => false,
                    'closeButton'       => true,
                    'preventDuplicates' => true,
                    'timeOut'           => 30000,
                    'extendedTimeOut'   => 15000,
                ],
            ],
        ],
    ],
];
