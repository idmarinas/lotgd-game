<?php

/**
 * Adds a news item for the current user.
 *
 * @param string $text
 * @param string $textDomain
 * @param bool   $hideFromBio
 *
 * @deprecated 5.3.0 Removed in future versions.
 */
function addnews($text, array $params = [], $textDomain = 'partial_news', ?bool $hideFromBio = null)
{
    global $session;

    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use "LotgdTool::addNews(string $text, array $params, string $textDomain, bool $hideFromBio);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);


    $user = $hideFromBio ? 0 : $session['user']['acctid'];

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\News::class);

    $newsEntity = $repository->hydrateEntity([
        'date'       => new \DateTime('now'),
        'text'       => $text,
        'arguments'  => $params,
        'textDomain' => $textDomain,
        'accountId'  => $user ?? 0,
    ]);

    \Doctrine::persist($newsEntity);
    \Doctrine::flush();
}
