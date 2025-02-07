<?php

declare(strict_types=1);

require_once 'src/constants.php';

use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\If_\ShortenElseIfRector;
use Rector\CodeQuality\Rector\Include_\AbsolutizeRequireAndIncludePathRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php56\Rector\FuncCall\PowToExpRector;
use Rector\Php70\Rector\Ternary\TernaryToSpaceshipRector;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
  ->withPaths([
	__DIR__ . '/cronjob',
	__DIR__ . '/lib',
	__DIR__ . '/public',
	__DIR__ . '/src',
	__DIR__ . '/tests',
  ])
  ->withPhp74Sets()
  ->withImportNames(true, true, true, true)
  ->withTypeCoverageLevel(0)
  ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/coreLotgd_Core_KernelDevDebugContainer.xml')
  ->withSets([
	SetList::DEAD_CODE,
	SetList::CODE_QUALITY,
	SetList::CODING_STYLE,
	DoctrineSetList::DOCTRINE_CODE_QUALITY,
	SymfonySetList::SYMFONY_44,
	SymfonySetList::SYMFONY_CODE_QUALITY,
  ])
  ->withSkip([
	  // Dirs and files
	  __DIR__ . '/src/core/Entity/*Translation.php',
	  __DIR__ . '/src/core/Twig/NodeVisitor',
	  // Rules
	  NewlineAfterStatementRector::class,
	  AbsolutizeRequireAndIncludePathRector::class,
	  EncapsedStringsToSprintfRector::class,
	  AddVoidReturnTypeWhereNoReturnRector::class,
	  SimplifyEmptyCheckOnEmptyArrayRector::class,
	  AddClosureVoidReturnTypeWhereNoReturnRector::class,
	  TernaryToSpaceshipRector::class,
	  PowToExpRector::class,
	  ExplicitReturnNullRector::class,
	  UseIdenticalOverEqualWithSameTypeRector::class,
	  StrictArraySearchRector::class,
	  DisallowedEmptyRuleFixerRector::class,
	  ShortenElseIfRector::class               => [
		__DIR__ . '/lib/showform.php',
		__DIR__ . '/public/login.php',
	  ],
	  RemoveAlwaysTrueIfConditionRector::class => [
		__DIR__ . '/public/forest.php',
		__DIR__ . '/public/common.php',
	  ],
  ])
;
