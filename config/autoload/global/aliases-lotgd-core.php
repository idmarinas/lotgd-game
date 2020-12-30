<?php

return [
    'service_manager' => [
        'aliases' => [
            //-- Added in version 4.1.0
            'Laminas\Form\Annotation\FormAnnotationBuilder'     => 'FormAnnotationBuilder',
            Laminas\Form\Annotation\AnnotationBuilder::class    => 'FormAnnotationBuilder',
            Laminas\Form\FormElementManager::class              => 'FormElementManager',
            Laminas\InputFilter\InputFilterPluginManager::class => 'InputFilterManager',
        ],
    ],
];
