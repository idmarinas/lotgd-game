<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Component;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem extends SymfonyFilesystem
{
    /**
     * List files in directory.
     *
     * @param string $dir Directory to scan in search of files
     *
     * @throws IOException When not found directory
     *
     * @return array
     */
    public function listDir(string $dir): array
    {
        if (! is_dir($dir))
        {
            throw new IOException(sprintf('Could not scan directory "%s", not found.', $dir));
        }

        return array_diff(scandir($dir), ['.', '..']);
    }
}
