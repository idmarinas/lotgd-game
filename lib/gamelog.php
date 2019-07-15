<?php

function gamelog($message, $category = 'general', $filed = false)
{
    global $session;

    $repository = \Doctrine::getRepository('LotgdCore:Gamelog');

    $entity = $repository->hydrateEntity([
        'message' => $message,
        'category' => $category,
        'filed' => ($filed ? 1 : 0),
        'date' => new \DateTime('now'),
        'who' => (int) $session['user']['acctid']
    ]);

    \Doctrine::persist($entity);
    \Doctrine::flush();
}
