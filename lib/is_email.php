<?php
// translator ready
// addnews ready
// mail ready
use Zend\Validator\EmailAddress;

function is_email($email)
{
    $validator = new EmailAddress();

    return $validator->isValid($email);
}
?>
