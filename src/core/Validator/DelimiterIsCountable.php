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

namespace Lotgd\Core\Validator;

use Zend\Validator\IsCountable;

class DelimiterIsCountable extends IsCountable
{
    /**
     * Options for the between validator
     *
     * @var array
     */
    protected $options = [
        'count' => null,
        'min' => null,
        'max' => null,
        'delimiter' => ','
    ];

    /**
     * Get delimiter option
     *
     * @return mixed
     */
    public function getDelimiter()
    {
        return $this->options['delimiter'];
    }

    /**
     * Set delimiter option
     *
     * @param string $value
     *
     * @return void
     */
    public function setDelimiter($value)
    {
        $this->options['delimiter'] = $value;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        if (is_string($value))
        {
            $value = explode($this->getDelimiter(), $value);
        }

        return parent::isValid($value);
    }
}
