<?php

/** @deprecated 5.3.0 Removed un future versions */
function gamelog($message, $category = 'general', $filed = false)
{
    global $session;

    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use LotgdLog::game($message, $category); instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $repository = \Doctrine::getRepository('LotgdCore:Gamelog');

    $entity = $repository->hydrateEntity([
        'message'  => $message,
        'category' => $category,
        'filed'    => ($filed ? 1 : 0),
        'date'     => new \DateTime('now'),
        'who'      => (int) $session['user']['acctid'],
    ]);

    \Doctrine::persist($entity);
    \Doctrine::flush();
}
