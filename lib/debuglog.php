<?php

// translator ready
// addnews ready
// mail ready

/**
 * Documentated by Catscradler
 * Add to the user's log
 * if $field $value and $consolidate have values, entry will be merged with existing line from today with identical $field.
 * otherwise, a new line will be added to the log.
 *
 * @param string $message     the text to be added
 * @param int    $target      acctid of the user on the receiving end of the event eg. the user who did NOT initiate PvP, gold transfer recipient (optional)
 * @param int    $user        acctid of the user the log entry is about (optional, defaults to current user)
 * @param string $field       the label for this line, appears as first word on this line in the log eg. healing, forestwin (optional)
 * @param int    $value       how much was gained or lost.  Only useful if also using $field and $consolidate (optional)
 * @param bool   $consolidate add $value to previous log lines with the same $field, keeping a running total for today (optional, defaults to true)
 *
 * @deprecated 5.3.0 Removed un future versions
 */
function debuglog($message, $target = false, $user = false, $field = false, $value = false, $consolidate = true)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use LotgdLog::debug($message, $target, $user, $field, $value, $consolidate); instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdLog::debug($message, $target, $user, $field, $value, $consolidate);
}
