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

namespace Lotgd\Core\Symfony\Validator;

use Symfony\Component\Validator\ConstraintValidatorFactory as SymfonyConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;

/**
 * Class ConstraintValidatorFactory.
 */
class ConstraintValidatorFactory extends SymfonyConstraintValidatorFactory
{
    public function addValidator(string $className, ConstraintValidatorInterface $validator): void
    {
        $this->validators[$className] = $validator;
    }
}
