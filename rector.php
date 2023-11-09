<?php

declare(strict_types=1);

require_once 'src/constants.php';

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\Include_\AbsolutizeRequireAndIncludePathRector;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\TwigSetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void
{
    $rectorConfig->paths([
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_74);
    $rectorConfig->importNames(true, false);
    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/coreLotgd_Core_KernelDevDebugContainer.xml');

    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(SetList::CODE_QUALITY);
    $rectorConfig->import(SetList::PHP_74);

    //-- Symfony Framework
    $rectorConfig->import(SymfonyLevelSetList::UP_TO_SYMFONY_44);
    $rectorConfig->import(TwigSetList::TWIG_240);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_25);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_ORM_29);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_CODE_QUALITY);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_COMMON_20);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_DBAL_210);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_DBAL_211);

    //-- Skip some rules/files ...
    $rectorConfig->skip([
        __DIR__.'/src/core/Twig/NodeVisitor',
        CallableThisArrayToAnonymousFunctionRector::class,
        AbsolutizeRequireAndIncludePathRector::class
    ]);
};
