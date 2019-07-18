<?php

/**
 * Adds a news item for the current user.
 *
 * @param string $text
 * @param array  $params
 * @param string $textDomain
 * @param bool   $hideFromBio
 */
function addnews($text, array $params = [], $textDomain = 'partial-news', bool $hideFromBio = null)
{
    global $session;

    $user = $hideFromBio ? 0 :$session['user']['acctid'];

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\News::class);

    $newsEntity = $repository->hydrateEntity([
        'date' => new \DateTime('now'),
        'text' => $text,
        'arguments' => $params,
        'textDomain' => $textDomain,
        'accountId' => $user ?? 0
    ]);

    \Doctrine::persist($newsEntity);
    \Doctrine::flush();
}
