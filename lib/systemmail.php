<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 6.1.0 deleted in 7.0.0 version. Use "LotgdKernel::get('lotgd_core.tool.system_mail')->send($to, $subject, $body, $from, $noemail)" */
function systemmail($to, $subject, $body, $from = 0, $noemail = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.1.0; and delete in 7.0.0 version. Use "LotgdKernel::get("lotgd_core.tool.system_mail")->send($to, $subject, $body, $from, $noemail);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.tool.system_mail')->send($to, $subject, $body, $from, $noemail);
}
