<?php

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

            //-- Added in version 4.1.0
            /* LAZY */ 'InputFilterManager'    => Laminas\InputFilter\InputFilterPluginManagerFactory::class, //-- Deprecated - Use Symfony Form instead
            /* LAZY */ 'FormAnnotationBuilder' => Laminas\Form\Annotation\AnnotationBuilderFactory::class, //-- Deprecated - Use Symfony Form instead
            /* LAZY */ 'FormElementManager'    => Laminas\Form\FormElementManagerFactory::class, //-- Deprecated - Use Symfony Form instead

            //-- Added in version 4.2.0
            /* LAZY */ 'Lotgd\Core\SymfonyForm'            => Factory\Form\SymfonyForm::class,

            //-- Added in version 4.4.0
            /* LAZY */ Lotgd\Core\EventManager\Event::class => Factory\EventManager\EventManager::class, //-- This factory no need migrate (Symfony have a event system)
            Lotgd\Core\EventManager\Hook::class             => Factory\EventManager\HookManager::class, //-- This factory no need migrate (Symfony have a event system)
            Lotgd\Core\Http\Request::class                  => Factory\Http\Request::class, //-- This factory no need in Kernel (Symfony do it)
            Lotgd\Core\Http\Response::class                 => Factory\Http\Response::class, //-- This factory no need in Kernel (Symfony do it)
            'webpack_encore.tag_renderer'                   => Factory\Template\TagRenderer::class, //-- This factory no need migrate
            'webpack_encore.packages'                       => Factory\Template\Packages::class, //-- This factory no need migrate

            //-- Added in version 4.5.0
            'webpack_encore.entrypoint_lookup_collection' => Factory\Template\EntrypointLookupCollection::class, //-- This factory no need migrate
        ],
    ],
];
