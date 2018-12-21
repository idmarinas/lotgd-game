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
}
