<?php

declare(strict_types=1);

require_once 'src/constants.php';

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/app/src',
		__DIR__ . '/factories',
		__DIR__ . '/public',
		__DIR__ . '/src',
		__DIR__ . '/tests',
	])
	->withPhpSets(false, false, false, false, true)
	->withPreparedSets(
		true,
		true,
		true,
		false,
		false,
		false,
		false,
		false,
		false,
		false,
		false,
		false,
		true,
		true,
		true,
		true
	)
	->withImportNames(true, true, true, true)
	->withTypeCoverageLevel(0)
	->withSets([
		SymfonySetList::SYMFONY_64,
		SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
	])
	->withRules([AddVoidReturnTypeWhereNoReturnRector::class])
	->withSkip([
		__DIR__ . '/src/core/Twig/NodeVisitor',
	])
;