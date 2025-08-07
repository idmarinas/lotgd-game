<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/cronjob',
		__DIR__ . '/lib',
		__DIR__ . '/public',
		__DIR__ . '/src',
		__DIR__ . '/tests',
	])
	->withPhp74Sets()
	->withImportNames()
	->withTypeCoverageLevel(0)
	->withDeadCodeLevel(0)
	->withCodeQualityLevel(0)
	->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/coreLotgd_Core_KernelDevDebugContainer.xml')
	->withSets([
		SetList::CODING_STYLE,
		SetList::PHP_80,
		SetList::PHP_81,
		SetList::PHP_82,
		SetList::PHP_83,
		SetList::PHP_84,
	])
	->withComposerBased(false, true, false, true)
//	->withConfiguredRule(EncapsedStringsToSprintfRector::class, [
//		EncapsedStringsToSprintfRector::ALWAYS => false,
//	])
	->withSkip([
		EncapsedStringsToSprintfRector::class,
		StrictArraySearchRector::class,
	])
;
