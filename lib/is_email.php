<?php

// translator ready
// addnews ready
// mail ready

/**
 * Check if given email is valid.
 *
 * @param string $email
 *
 * @return bool
 */
function is_email($email)
{
    $validator = new Laminas\Validator\EmailAddress();

    return $validator->isValid($email);
}
