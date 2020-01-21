<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Form\Element;

use Lotgd\Core\Form\LotgdElementFactoryInterface;
use Lotgd\Core\Pattern as PatternCore;
use Zend\Form\Element\Select;

class LotgdTheme extends Select implements LotgdElementFactoryInterface
{
    use PatternCore\ThemeList;

    /**
     * Prepare element.
     *
     * @return $this
     */
    public function prepare()
    {
        $this->setValueOptions($this->getThemeList());

        return $this;
    }
}
