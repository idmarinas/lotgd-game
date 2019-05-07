<?php

namespace Lotgd\Core\Validator\Db;

/**
 * Class that validates if objects does not exist in a given repository with a given list of matched fields.
 *
 * @license MIT
 *
 * @see    http://www.doctrine-project.org/
 *
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class NoObjectExists extends ObjectExists
{
    /**
     * Error constants.
     */
    const ERROR_OBJECT_FOUND = 'objectFound';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::ERROR_OBJECT_FOUND => "An object matching '%value%' was found",
    ];

    /**
     * {@inheritdoc}
     */
    public function isValid($value): bool
    {
        $cleanedValue = $this->cleanSearchValue($value);
        $match = $this->objectRepository->findOneBy($cleanedValue);

        if (is_object($match))
        {
            $this->error(self::ERROR_OBJECT_FOUND, $value);

            return false;
        }

        return true;
    }
}
