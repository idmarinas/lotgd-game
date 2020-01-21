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

namespace Lotgd\Core\Form\Element;

use Lotgd\Core\Form\LotgdElementFactoryInterface;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Pattern as PatternCore;
use Zend\Form\Element\Select;

class ServerLanguage extends Select implements LotgdElementFactoryInterface
{
    use PatternCore\Container;

    /**
     * Valid languages for server.
     *
     * @var array
     */
    protected $validLanguages = [];

    /**
     * Added languages.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->validLanguages = include 'data/form/core/grotto/configuration/languages.php';
    }

    /**
     * Prepare element
     *
     * @return $this
     */
    public function prepare()
    {
        //-- Get languages available in server.
        $settings = $this->getContainer(Settings::class);
        $server = explode(',', $settings->getSetting('serverlanguages'));

        $languages = [];

        foreach($server as $lng)
        {
            $languages[$lng] = $this->validLanguages[$lng];
        }

        $this->setValueOptions($languages);

        return $this;
    }
}
