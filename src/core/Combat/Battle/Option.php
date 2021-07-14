<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait Option
{
    /**
     * Options of battle.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Get all of options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set the value of options (replace all options).
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set an option by name (replace if exists).
     *
     * @param mixed $value
     */
    public function setOption(string $key, $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get an option by name.
     *
     * @param mixed $default
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Set type (same as battle zone).
     */
    public function setOptionType(string $type): self
    {
        $this->options['type'] = $type;

        return $this;
    }

    /**
     * Get type (same as battle zone).
     */
    public function getOptionType(): ?string
    {
        return $this->options['type'] ?? null;
    }

    /**
     * Mark battle that as active.
     */
    public function optionsBattleActive(): self
    {
        $this->options['isBattleActive'] = true;

        return $this;
    }

    /**
     * Mark battle that as deactive.
     */
    public function optionsBattleDeactive(): self
    {
        $this->options['isBattleActive'] = false;

        return $this;
    }
}
