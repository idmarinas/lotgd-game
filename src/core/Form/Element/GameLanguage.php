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
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Pattern as PatternCore;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class GameLanguage extends Select implements LotgdElementFactoryInterface, InputProviderInterface
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
     * @param array  $options
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->validLanguages = include 'data/form/core/grotto/configuration/languages.php';
    }

    /**
     * Prepare element.
     *
     * @return $this
     */
    public function prepare()
    {
        //-- Get languages available in server.
        $settings = $this->getContainer(Settings::class);
        $server = explode(',', $settings->getSetting('serverlanguages'));

        $languages = [];

        foreach ($server as $lng)
        {
            $languages[$lng] = $this->validLanguages[$lng];
        }

        $this->setValueOptions($languages);

        return $this;
    }

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\InArray::class,
                    'options' => [
                        'haystack' => array_keys($this->validLanguages)
                    ]
                ]
            ],
        ];
    }
}
