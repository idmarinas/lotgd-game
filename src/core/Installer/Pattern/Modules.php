<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Installer\Pattern;

trait Modules
{
    protected $skipModules = false;
    protected $modules = [];

    /**
     * No install modules
     *
     * @return $this
     */
    public function skipModulesOn(): self
    {
        $this->skipModules = true;

        return $this;
    }

    /**
     * No install modules
     *
     * @return $this
     */
    public function skipModulesOff(): self
    {
        $this->skipModules = false;

        return $this;
    }

    /**
     * Get if installation of modules is ignored
     *
     * @return bool
     */
    public function skipModules(): bool
    {
        return $this->skipModules;
    }

    /**
     * Set list of modules to process
     *
     * @param array $modules
     *
     * @return self
     */
    public function setModules(array $modules): self
    {
        $this->modules = $modules;

        return $this;
    }

    /**
     * Get list of modules to process
     *
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
