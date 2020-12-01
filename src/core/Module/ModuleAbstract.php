<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Module;

abstract class ModuleAbstract
{
    const VERSION = '1.0.0';
    const VERSION_NUMBER = 10000; // 1.00.00

    /**
     * Get info of module.
     */
    abstract public function getInfo(): array;

    /**
     * Process run of module.
     */
    abstract public function run(): void;

    /**
     * Install module.
     */
    abstract public function install(): bool;

    /**
     * Uninstall module.
     */
    abstract public function uninstall(): bool;

    /**
     * Get version string of module.
     * Ejem: 1.0.0.
     */
    final public function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * Get version number of module.
     * Ejem: 10000.
     */
    final public function getVersionNumber(): int
    {
        return static::VERSION_NUMBER;
    }

    /**
     * Get class name.
     */
    final public function getClassName(): string
    {
        return static::class;
    }
}
