<?php

use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Service;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Lotgd\Core\Factory;

return [
    'service_manager' => [
        'factories' => [
            //-- Added in version 3.0.0
            Lotgd\Core\Character\Stats::class  => InvokableFactory::class,
            Lotgd\Core\Db\Dbwrapper::class     => Factory\Db\Dbwrapper::class, //-- Deleted in version 5.0.0
            Lotgd\Core\Lib\Settings::class     => Factory\Lib\Settings::class,
            Lotgd\Core\Output\Color::class     => InvokableFactory::class,
            Lotgd\Core\Output\Collector::class => Factory\Output\Collector::class,
            Lotgd\Core\Template\Theme::class   => Factory\Template\Theme::class,
            Lotgd\Core\Http::class             => InvokableFactory::class, //-- Deleted in version 5.0.0

            //-- Added in version 4.0.0
            Lotgd\Core\Component\FlashMessages::class          => Factory\Component\FlashMessages::class,
            Lotgd\Core\Installer\Install::class                => Factory\Installer\Install::class,
            Lotgd\Core\Navigation\Navigation::class            => Factory\Navigation\Navigation::class,
            Lotgd\Core\Navigation\AccessKeys::class            => InvokableFactory::class,
            Lotgd\Core\Output\Censor::class                    => Factory\Output\Censor::class,
            Lotgd\Core\Output\Commentary::class                => Factory\Output\Commentary::class,
            Lotgd\Core\Output\Code::class                      => InvokableFactory::class,
            Lotgd\Core\Output\Format::class                    => Factory\Output\Format::class,
            Lotgd\Core\Pvp\Listing::class                      => Factory\Pvp\Listing::class,
            Lotgd\Core\Tool\Sanitize::class                    => Factory\Tool\Sanitize::class,
            Lotgd\Core\Translator\Translator::class            => Factory\Translator\Translator::class,
            Lotgd\Core\Session::class                          => Factory\Session::class,
            Laminas\I18n\Translator\LoaderPluginManager::class => Factory\Translator\LoaderPluginManager::class,
            Laminas\Session\Config\ConfigInterface::class      => Laminas\Session\Service\SessionConfigFactory::class,
            Laminas\Session\ManagerInterface::class            => Laminas\Session\Service\SessionManagerFactory::class,
            Laminas\Session\Storage\StorageInterface::class    => Laminas\Session\Service\StorageFactory::class,

            //-- Added in version 4.1.0
            'InputFilterManager'    => Laminas\InputFilter\InputFilterPluginManagerFactory::class,
            'FormAnnotationBuilder' => Laminas\Form\Annotation\AnnotationBuilderFactory::class,
            'FormElementManager'    => Laminas\Form\FormElementManagerFactory::class,

            //-- Added in version 4.2.0
            'doctrine.cli'                                   => 'DoctrineModule\Service\CliFactory',
            CliConfigurator::class                           => Service\CliConfiguratorFactory::class,
            'Doctrine\ORM\EntityManager'                     => Service\EntityManagerAliasCompatFactory::class,
            Lotgd\Core\Doctrine\Extension\TablePrefix::class => Factory\Doctrine\Extension\TablePrefix::class,
            Lotgd\Core\Jaxon::class                          => Factory\Component\Jaxon::class,
            Gedmo\Translatable\TranslatableListener::class   => Factory\Translator\TranslatableListener::class,
            'Lotgd\Core\SymfonyForm'                         => Factory\Form\SymfonyForm::class,

            //-- Added in version 4.4.0
            Lotgd\Core\EventManager\Event::class => Factory\EventManager\EventManager::class,
            Lotgd\Core\EventManager\Hook::class  => Factory\EventManager\HookManager::class,
            Lotgd\Core\Http\Request::class       => InvokableFactory::class,
            Lotgd\Core\Http\Response::class      => Factory\Http\Response::class,
            'webpack_encore.tag_renderer'        => Factory\Template\TagRenderer::class,
            'webpack_encore.packages'            => Factory\Template\Packages::class,

            //-- Added in version 4.5.0
            Laminas\View\Helper\HeadLink::class           => InvokableFactory::class,
            Laminas\View\Helper\HeadMeta::class           => Factory\View\Helper\HeadMeta::class,
            Laminas\View\Helper\HeadScript::class         => InvokableFactory::class,
            Laminas\View\Helper\HeadStyle::class          => InvokableFactory::class,
            Laminas\View\Helper\HeadTitle::class          => InvokableFactory::class,
            Laminas\View\Helper\InlineScript::class       => InvokableFactory::class,
            Lotgd\Core\Template\Params::class             => Factory\Template\Params::class,
            'webpack_encore.entrypoint_lookup_collection' => Factory\Template\EntrypointLookupCollection::class,

            //-- Added in version 4.6.0
            Laminas\View\Helper\BasePath::class => Factory\View\Helper\BasePath::class,
        ],
    ],
];
