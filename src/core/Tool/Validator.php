<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Tool;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class that simplifies data checking. Returning true or false.
 */
class Validator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function isMail(?string $value): bool
    {
        $errors = $this->validator->validate($value, [
            new Assert\NotBlank(),
            new Assert\NotNull(),
            new Assert\Email(),
        ]);

        return ! (bool) \count($errors);
    }
}
