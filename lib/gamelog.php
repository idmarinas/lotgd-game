<?php

/** @deprecated 5.3.0 Removed un future versions */
function gamelog($message, $category = 'general', $filed = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use LotgdLog::game($message, $category); instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdLog::game($message, $category, $filed);
}
