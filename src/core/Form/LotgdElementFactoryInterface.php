<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * All files in "Lotgd\Core\Twig\Extension\Form" are based in zend form view classes.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Form;

interface LotgdElementFactoryInterface
{
    /**
     * Prepare element for inject.
     */
    public function prepare();
}
