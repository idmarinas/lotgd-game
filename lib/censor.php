<?php

// translator ready
// addnews ready
// mail ready
function soap($input, $debug = false, $skiphook = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0, use new censor system.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $censor = \LotgdLocator::get(\Lotgd\Core\Output\Censor::class);

    return $censor->filter($input);
}

function good_word_list()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0. Not do nothing.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return '';
}

function nasty_word_list()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and not do nothing; and delete in version 4.1.0. Not do nothing.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return '';
}
