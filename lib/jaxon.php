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
            'alert' => 'toastr',
            'modal' => 'semantic'
        ],
        'classes' => [
            'semantic' => \Lotgd\Core\Jaxon\Library\Semantic\Modal::class,
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

$lotgdJaxon->useComposerAutoloader();

//-- Register all class of Lotgd in dir "src/ajax/core"
$lotgdJaxon->addClassDir(realpath(__DIR__) . '/../src/ajax/core', 'Lotgd\\Ajax\\Core\\');
$lotgdJaxon->registerClasses();

//-- Register all custom class (Available globally)
$lotgdJaxon->addClassDir(realpath(__DIR__) . '/../lotgd/ajax', 'Lotgd\\Ajax\\Local\\');
$lotgdJaxon->registerClasses();

$lotgdJaxon->plugin('dialog')->registerClasses();
