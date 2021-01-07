<?php

use Lotgd\Core\Factory;

return [
    'service_manager' => [
        'factories' => [
            //-- Added in version 3.0.0 - Deleted in version 5.0.0
            /* LAZY */ Lotgd\Core\Db\Dbwrapper::class => Factory\Db\Dbwrapper::class, //-- Deprecated
            Lotgd\Core\Template\Theme::class          => Factory\Template\Theme::class,

            //-- Added in version 4.0.0  - Deleted in version 5.0.0
            Lotgd\Core\Translator\Translator::class            => Factory\Translator\Translator::class, //-- Deprecated - migrate to Symfony Translation
            Laminas\I18n\Translator\LoaderPluginManager::class => Factory\Translator\LoaderPluginManager::class,

            //-- Added in version 4.1.0 - Deleted in version 5.0.0
            /* LAZY */ 'InputFilterManager'    => Laminas\InputFilter\InputFilterPluginManagerFactory::class, //-- Deprecated - Use Symfony Form instead
            /* LAZY */ 'FormAnnotationBuilder' => Laminas\Form\Annotation\AnnotationBuilderFactory::class, //-- Deprecated - Use Symfony Form instead
            /* LAZY */ 'FormElementManager'    => Laminas\Form\FormElementManagerFactory::class, //-- Deprecated - Use Symfony Form instead

            //-- Added in version 4.2.0 - Deleted in version 5.0.0
            /* LAZY */ 'Lotgd\Core\SymfonyForm' => Factory\Form\SymfonyForm::class, //-- This factory no need migrate, Symfony have a Form service LotgdKernel::get('form.factory')

            //-- Added in version 4.4.0 - Deleted in version 5.0.0
            'webpack_encore.tag_renderer' => Factory\Template\TagRenderer::class, //-- This factory no need migrate
            'webpack_encore.packages'     => Factory\Template\Packages::class, //-- This factory no need migrate

            //-- Added in version 4.5.0 - Deleted in version 5.0.0
            'webpack_encore.entrypoint_lookup_collection' => Factory\Template\EntrypointLookupCollection::class, //-- This factory no need migrate
        ],
    ],
];
