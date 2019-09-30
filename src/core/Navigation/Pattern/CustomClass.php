<?php

/**
* This file is part of Legend of the Green Dragon.
*
* @see https://github.com/idmarinas/lotgd-game
*
* @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
* @author IDMarinas
*
* @since 4.0.0
*/

namespace Lotgd\Core\Navigation\Pattern;

trait CustomClass
{
    /**
     * Class name for header.
     *
     * @var string
     */
    protected $classHeader = 'navhead';

    /**
     * Class name for nav.
     *
     * @var string
     */
    protected $classNav = 'nav';

    /**
     * Get class name for header.
     *
     * @return  string
     */
    public function getClassHeader()
    {
        return $this->classHeader;
    }

    /**
     * Set class name for header.
     *
     * @param  string  $classHeader  Class name for header.
     *
     * @return  self
     */
    public function setClassHeader(string $classHeader)
    {
        $this->classHeader = $classHeader;

        return $this;
    }

    /**
     * Get class name for nav.
     *
     * @return  string
     */
    public function getClassNav()
    {
        return $this->classNav;
    }

    /**
     * Set class name for nav.
     *
     * @param  string  $classNav  Class name for nav.
     *
     * @return  self
     */
    public function setClassNav(string $classNav)
    {
        $this->classNav = $classNav;

        return $this;
    }
}
