<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Form\Element;

use Laminas\Form\Element\Select;
use Laminas\InputFilter\InputProviderInterface;
use Lotgd\Core\Form\LotgdElementFactoryInterface;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Pattern as PatternCore;

class PetitionType extends Select implements LotgdElementFactoryInterface, InputProviderInterface
{
    use PatternCore\Container;

    /**
     * Prepare element.
     *
     * @return $this
     */
    public function prepare(): self
    {
        //-- Get petitions available in server.
        $settings  = $this->getService(Settings::class);
        $petitions = \explode(',', $settings->getSetting('petition_types'));

        $choices = [];

        foreach ($petitions as $petition)
        {
            $choices[$petition] = $petition;
        }

        $this->setValueOptions($choices);
        $this->setOption('translator_text_domain', 'jaxon_petition');

        return $this;
    }
}
