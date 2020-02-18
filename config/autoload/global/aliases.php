<?php

return [
    'service_manager' => [
        'aliases' => [
            Zend\Session\SessionManager::class => Zend\Session\ManagerInterface::class,

            //-- Added in version 4.1.0
            'Zend\Form\Annotation\FormAnnotationBuilder' => 'FormAnnotationBuilder',
            Zend\Form\Annotation\AnnotationBuilder::class => 'FormAnnotationBuilder',
            Zend\Form\FormElementManager::class => 'FormElementManager',
            Zend\InputFilter\InputFilterPluginManager::class => 'InputFilterManager',

            //-- Added in version 4.2.0
            'Lotgd\Core\Db\Doctrine' => 'Doctrine\ORM\EntityManager'
        ]
    ]
];
