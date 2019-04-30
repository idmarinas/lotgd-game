<?php

\LotgdNavigation::addHeader('category.options');

if ($session['user']['clanrank'] < CLAN_OFFICER)
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('secction.motd.messagess.error', [], $textDomain));

    return redirect('clan.php');
}

$acctRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
$clanRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class);

$result = $acctRepository->getClanAuthorNameOfMotdDescFromAcctId($claninfo['motdauthor'], $claninfo['descauthor']);
$params['motdAuthorName'] = $result['motdauthname'];
$params['descAuthorName'] = $result['descauthname'];
unset($result);

$clanmotd = \LotgdSanitize::mbSanitize(\mb_substr(\LotgdHttp::getPost('clanmotd'), 0, 4096));
$clandesc = \LotgdSanitize::mbSanitize(\mb_substr(\LotgdHttp::getPost('clandesc'), 0, 4096));
$customsay = \LotgdSanitize::mbSanitize(\mb_substr(\LotgdHttp::getPost('customsay'), 0, 15));

$clanEntity = $clanRepository->find($claninfo['clanid']);

$invalidateCache = false;
if ($clanmotd && $claninfo['clanmotd'] != $clanmotd)
{
    $invalidateCache = true;
    $clanEntity->setMotdauthor($session['user']['acctid'])
        ->setClanmotd($clanmotd)
    ;
    $claninfo['motdauthor'] = $session['user']['acctid'];
    $claninfo['clanmotd'] = $clanmotd;

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('section.motd.messagess.saved.motd', [], $textDomain));
}

if ($clandesc && $claninfo['clandesc'] != $clandesc)
{
    $invalidateCache = true;
    $clanEntity->setDescauthor($session['user']['acctid'])
        ->setClandesc($clandesc)
    ;
    $claninfo['descauthor'] = $session['user']['acctid'];
    $claninfo['clandesc'] = $clandesc;

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('section.motd.messagess.saved.desc', [], $textDomain));
}

if ($customsay && $claninfo['customsay'] != $customsay)
{
    $invalidateCache = true;
    $clanEntity->setCustomsay($customsay);
    $claninfo['customsay'] = $customsay;

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('section.motd.messagess.saved.say', [], $textDomain));
}

if ($invalidateCache)
{
    invalidatedatacache("clandata-{$claninfo['clanid']}");
}

\Doctrine::persist($clanEntity);
\Doctrine::flush();

$params['clanInfo'] = $claninfo;

\LotgdNavigation::addNav('nav.motd.return', 'clan.php');
