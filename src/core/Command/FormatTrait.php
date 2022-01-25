<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.6.0
 */

namespace Lotgd\Core\Command;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Symfony\Component\Console\Helper\Helper;

trait FormatTrait
{
    protected static function formatPath(string $path, string $baseDir): string
    {
        return \preg_replace('~^'.\preg_quote($baseDir, '~').'~', '.', $path);
    }

    protected static function formatFileSize(string $path): string
    {
        if (\is_file($path))
        {
            $size = \filesize($path) ?: 0;
        }
        else
        {
            $size = 0;

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS)) as $file)
            {
                $size += $file->getSize();
            }
        }

        return Helper::formatMemory($size);
    }
}
