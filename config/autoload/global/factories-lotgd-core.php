<?php

use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Service;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Lotgd\Core\Factory;

return [
    'service_manager' => [
        'factories' => [
            //-- Added in version 3.0.0
            Lotgd\Core\Character\Stats::class             => InvokableFactory::class,
            /* LAZY */ Lotgd\Core\Db\Dbwrapper::class     => Factory\Db\Dbwrapper::class, //-- Deprecated - Deleted in version 5.0.0
            Lotgd\Core\Lib\Settings::class                => Factory\Lib\Settings::class,
            Lotgd\Core\Output\Color::class                => InvokableFactory::class,
            /* LAZY */ Lotgd\Core\Output\Collector::class => Factory\Output\Collector::class, //-- Deprecated
            Lotgd\Core\Template\Theme::class              => Factory\Template\Theme::class,
            /* LAZY */ Lotgd\Core\Http::class             => InvokableFactory::class, //-- Deprecated - Deleted in version 5.0.0

            //-- Added in version 4.0.0
            /* LAZY */ Lotgd\Core\Installer\Install::class     => Factory\Installer\Install::class,
            Lotgd\Core\Navigation\Navigation::class            => Factory\Navigation\Navigation::class,
            Lotgd\Core\Navigation\AccessKeys::class            => InvokableFactory::class,
            /* LAZY */ Lotgd\Core\Output\Censor::class         => Factory\Output\Censor::class,
            /* LAZY */ Lotgd\Core\Output\Commentary::class     => Factory\Output\Commentary::class,
            Lotgd\Core\Output\Code::class                      => InvokableFactory::class,
            Lotgd\Core\Output\Format::class                    => Factory\Output\Format::class,
            /* LAZY */ Lotgd\Core\Pvp\Listing::class           => Factory\Pvp\Listing::class,
            Lotgd\Core\Tool\Sanitize::class                    => Factory\Tool\Sanitize::class,
            Lotgd\Core\Translator\Translator::class            => Factory\Translator\Translator::class, //-- Deprecated - migrate to Symfony Translation
            Laminas\I18n\Translator\LoaderPluginManager::class => Factory\Translator\LoaderPluginManager::class,
            Laminas\Session\Config\ConfigInterface::class      => Laminas\Session\Service\SessionConfigFactory::class,
            Laminas\Session\ManagerInterface::class            => Laminas\Session\Service\SessionManagerFactory::class,
            Laminas\Session\Storage\StorageInterface::class    => Laminas\Session\Service\StorageFactory::class,

            //-- Added in version 4.1.0
            /* LAZY */ 'InputFilterManager'    => Laminas\InputFilter\InputFilterPluginManagerFactory::class, //-- Deprecated - Use Symfony Form instead
            /* LAZY */ 'FormAnnotationBuilder' => Laminas\Form\Annotation\AnnotationBuilderFactory::class, //-- Deprecated - Use Symfony Form instead
            /* LAZY */ 'FormElementManager'    => Laminas\Form\FormElementManagerFactory::class, //-- Deprecated - Use Symfony Form instead

            //-- Added in version 4.2.0
            /* LAZY */ 'doctrine.cli'                                   => 'DoctrineModule\Service\CliFactory',
            /* LAZY */ CliConfigurator::class                           => Service\CliConfiguratorFactory::class,
            /* LAZY */ 'Doctrine\ORM\EntityManager'                     => Service\EntityManagerAliasCompatFactory::class, //-- Deprecated Factory - use \LotgdKernel::get('doctrine.orm.entity_manager)
            /* LAZY */ Lotgd\Core\Doctrine\Extension\TablePrefix::class => Factory\Doctrine\Extension\TablePrefix::class, //-- Deprecated Factory
            Lotgd\Core\Jaxon::class                                     => Factory\Component\Jaxon::class,
            Gedmo\Translatable\TranslatableListener::class              => Factory\Translator\TranslatableListener::class, //-- Deprecated Factory
            /* LAZY */ 'Lotgd\Core\SymfonyForm'                         => Factory\Form\SymfonyForm::class,

            //-- Added in version 4.4.0
            /* LAZY */ Lotgd\Core\EventManager\Event::class => Factory\EventManager\EventManager::class,
            Lotgd\Core\EventManager\Hook::class             => Factory\EventManager\HookManager::class,
            Lotgd\Core\Http\Request::class                  => Factory\Http\Request::class,
            Lotgd\Core\Http\Response::class                 => Factory\Http\Response::class,
            'webpack_encore.tag_renderer'                   => Factory\Template\TagRenderer::class,
            'webpack_encore.packages'                       => Factory\Template\Packages::class,

            //-- Added in version 4.5.0
            Laminas\View\Helper\HeadLink::class           => InvokableFactory::class,
            Laminas\View\Helper\HeadMeta::class           => Factory\View\Helper\HeadMeta::class,
            Laminas\View\Helper\HeadScript::class         => InvokableFactory::class,
            Laminas\View\Helper\HeadStyle::class          => InvokableFactory::class,
            Laminas\View\Helper\HeadTitle::class          => InvokableFactory::class,
            Laminas\View\Helper\InlineScript::class       => InvokableFactory::class,
            Lotgd\Core\Template\Params::class             => InvokableFactory::class,
            'webpack_encore.entrypoint_lookup_collection' => Factory\Template\EntrypointLookupCollection::class,

            //-- Added in version 4.6.0
            /* LAZY */ Laminas\View\Helper\BasePath::class => Factory\View\Helper\BasePath::class,
        ],
    ],
];
