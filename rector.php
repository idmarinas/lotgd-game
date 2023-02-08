<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\Include_\AbsolutizeRequireAndIncludePathRector;
use Rector\CodeQuality\Rector\If_\ShortenElseIfRector;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\TwigSetList;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Rector\MethodCall\EntityAliasToClassConstantReferenceRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;

return static function (RectorConfig $rectorConfig): void
{
    $rectorConfig->paths([
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->autoloadPaths([
        __DIR__ . '/src/functions.php',
    ]);

    $rectorConfig->bootstrapFiles([
        __DIR__ . '/src/constants.php',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_74);
    $rectorConfig->importNames(true, false);
    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/coreLotgd_Core_KernelDevDebugContainer.xml');

    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(SetList::CODE_QUALITY);
    $rectorConfig->import(LevelSetList::UP_TO_PHP_74);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);

    //-- Symfony Framework
    $rectorConfig->import(SymfonyLevelSetList::UP_TO_SYMFONY_44);
    $rectorConfig->import(TwigSetList::TWIG_240);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_25);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_ORM_29);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_CODE_QUALITY);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_COMMON_20);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_DBAL_210);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_DBAL_211);

    $rectorConfig->ruleWithConfiguration(EntityAliasToClassConstantReferenceRector::class, [
        EntityAliasToClassConstantReferenceRector::ALIASES_TO_NAMESPACES => [
            'LotgdCore' => 'Lotgd\Core\Entity',
            'LotgdLocal' => 'Lotgd\Local\Entity',
        ]
    ]);

    //-- Skip some rules/files ...
    $rectorConfig->skip([
        __DIR__.'/src/core/Twig/NodeVisitor',
        ShortenElseIfRector::class,
        EventListenerToEventSubscriberRector::class,
        CallableThisArrayToAnonymousFunctionRector::class,
        AbsolutizeRequireAndIncludePathRector::class
    ]);
};
