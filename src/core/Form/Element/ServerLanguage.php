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

use Lotgd\Core\Filter as LotgdFilter;
use Lotgd\Core\Validator as LotgdValidator;
use Laminas\Form\Element\Select;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator;

class ServerLanguage extends Select implements InputProviderInterface
{
    /**
     * Valid languages for server.
     *
     * @var array
     */
    protected $validLanguages = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->validLanguages = include 'data/form/core/grotto/configuration/languages.php';

        $this->setValueOptions($this->validLanguages);
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
            'filters' => [
                ['name' => LotgdFilter\ArrayToComaSeparator::class]
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => LotgdValidator\DelimiterIsCountable::class,
                    'options' => [
                        'delimiter' => ',', //-- Default value is ","
                        'max' => 85
                    ]
                ]
            ],
        ];
    }
}
