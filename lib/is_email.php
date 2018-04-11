<?php
// translator ready
// addnews ready
// mail ready

/**
 * Check if given email is valid
 *
 * @param string $email
 *
 * @return boolean
 */
function is_email($email)
{
    $validator = new Zend\Validator\EmailAddress;

    return $validator->isValid($email);
}
