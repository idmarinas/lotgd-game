<?php

declare(strict_types=1);

require_once 'src/constants.php';

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\Include_\AbsolutizeRequireAndIncludePathRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\TwigSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void
{
    // get parameters
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_73);

    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::PHP_74);

    //-- Symfony Framework
    $containerConfigurator->import(SymfonySetList::SYMFONY_44);
    $containerConfigurator->import(TwigSetList::TWIG_240);
    $containerConfigurator->import(DoctrineSetList::DOCTRINE_25);

    //-- Skip some rules/files ...
    $parameters->set(Option::SKIP, [
        __DIR__.'/src/core/Twig/NodeVisitor',
        CallableThisArrayToAnonymousFunctionRector::class,
        AbsolutizeRequireAndIncludePathRector::class
    ]);
};
