<?php

global $lotgdJaxon;

$lotgdJaxon = new Jaxon\Jaxon();
$lotgdJaxon->setOptions([
    'core' => [
        'request' => [
            'uri' => 'jaxon.php'
        ]
    ],
    'dialogs' => [
        'default' => [
            'alert' => 'toastr'
        ],
        'toastr' => [
            'options' => [
                'progressBar' => true,
                'newestOnTop' => true,
                'closeButton' => true,
                'preventDuplicates' => true
            ]
        ]
    ]
]);

//-- Register all class of Lotgd in dir "lib/jaxon/class"
$lotgdJaxon->addClassDir(realpath(__DIR__) . '/class', 'Lotgd\\Ajax');
$lotgdJaxon->registerClasses();

//-- Register all custom class (Available globally)
//-- Put files here if you need that this custom jaxon ajax are available globally
$lotgdJaxon->addClassDir(realpath(__DIR__) . '/../../jaxon', 'Global\\Ajax');
$lotgdJaxon->registerClasses();
