<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Installer\Pattern;

trait Progress
{
    protected $dataInserted = false;
    protected $dataUpgradesInserted = false;

    /**
     * Data are inserted in database.
     *
     * @return self
     */
    public function dataInsertedOn(): self
    {
        $this->dataInserted = true;

        return $this;
    }

    /**
     * Data NOT are inserted in database.
     *
     * @return self
     */
    public function dataInsertedOff(): self
    {
        $this->dataInserted = false;

        return $this;
    }

    /**
     * Get if data are inserted in database.
     *
     * @return bool
     */
    public function dataInserted(): bool
    {
        return $this->dataInserted;
    }

    /**
     * Data of upgrades are inserted in database.
     *
     * @return self
     */
    public function dataUpgradesInsertedOn(): self
    {
        $this->dataUpgradesInserted = true;

        return $this;
    }

    /**
     * Data of upgrades NOT are inserted in database.
     *
     * @return self
     */
    public function dataUpgradesInsertedOff(): self
    {
        $this->dataUpgradesInserted = false;

        return $this;
    }

    /**
     * Get if data of upgrades are inserted in database.
     *
     * @return bool
     */
    public function dataUpgradesInserted(): bool
    {
        return $this->dataUpgradesInserted;
    }
}
