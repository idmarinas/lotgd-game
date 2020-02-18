<?php

use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Service;
use Lotgd\Core\Factory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            //-- Added in version 3.0.0
            Lotgd\Core\Character\Stats::class => InvokableFactory::class,
            Lotgd\Core\Db\Dbwrapper::class => Factory\Db\Dbwrapper::class,
            Lotgd\Core\Lib\Settings::class => Factory\Lib\Settings::class,
            Lotgd\Core\Output\Color::class => InvokableFactory::class,
            Lotgd\Core\Output\Collector::class => Factory\Output\Collector::class,
            Lotgd\Core\Template\Theme::class => Factory\Template\Theme::class,
            Lotgd\Core\Http::class => InvokableFactory::class,

            //-- Added in version 4.0.0
            Lotgd\Core\Component\FlashMessages::class => Factory\Component\FlashMessages::class,
            Lotgd\Core\Installer\Install::class => Factory\Installer\Install::class,
            Lotgd\Core\Navigation\Navigation::class => Factory\Navigation\Navigation::class,
            Lotgd\Core\Navigation\AccessKeys::class => InvokableFactory::class,
            Lotgd\Core\Output\Censor::class => Factory\Output\Censor::class,
            Lotgd\Core\Output\Commentary::class => Factory\Output\Commentary::class,
            Lotgd\Core\Output\Code::class => InvokableFactory::class,
            Lotgd\Core\Output\Format::class => Factory\Output\Format::class,
            Lotgd\Core\Pvp\Listing::class => Factory\Pvp\Listing::class,
            Lotgd\Core\Tool\Sanitize::class => Factory\Tool\Sanitize::class,
            Lotgd\Core\Translator\Translator::class => Factory\Translator\Translator::class,
            Lotgd\Core\Session::class => Factory\Session::class,
            Zend\I18n\Translator\LoaderPluginManager::class => Factory\Translator\LoaderPluginManager::class,
            Zend\Session\Config\ConfigInterface::class => Zend\Session\Service\SessionConfigFactory::class,
            Zend\Session\ManagerInterface::class => Zend\Session\Service\SessionManagerFactory::class,
            Zend\Session\Storage\StorageInterface::class => Zend\Session\Service\StorageFactory::class,

            //-- Added in version 4.1.0
            'InputFilterManager' => Zend\InputFilter\InputFilterPluginManagerFactory::class,
            'FormAnnotationBuilder' => Zend\Form\Annotation\AnnotationBuilderFactory::class,
            'FormElementManager' => Zend\Form\FormElementManagerFactory::class,

            //-- Added in version 4.2.0
            'doctrine.cli' => 'DoctrineModule\Service\CliFactory',
            CliConfigurator::class => Service\CliConfiguratorFactory::class,
            'Doctrine\ORM\EntityManager' => Service\EntityManagerAliasCompatFactory::class,
            Lotgd\Core\Doctrine\Extension\TablePrefix::class => Factory\Doctrine\Extension\TablePrefix::class,
        ]
    ]
];
