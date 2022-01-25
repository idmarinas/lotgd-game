<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_EDIT_COMMENTS);

$textDomain = 'page_bios';

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$op     = (string) LotgdRequest::getQuery('op');
$userid = (int) LotgdRequest::getQuery('charid');

$repository = Doctrine::getRepository('LotgdCore:Avatar');

$params = [
    'textDomain' => $textDomain,
];

if ('block' == $op || 'unblock' == $op)
{
    $message      = "player.{$op}.fail";
    $flashMessage = 'addErrorMessage';

    if ($repository->blockCharacterBio($userid))
    {
        $message      = "player.{$op}.success";
        $flashMessage = 'addSuccessMessage';

        $subj = ["mail.{$op}.subject", [], $textDomain];
        $msg  = ["mail.{$op}.message", [], $textDomain];

        systemmail($userid, $subj, $msg);
    }

    LotgdFlashMessages::{$flashMessage}(LotgdTranslator::t($message, [], $textDomain));
}

$params['unblocked'] = $repository->getCharactersUnblockedBio();
$params['blocked']   = $repository->getCharactersBlockedBio();

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addNav('bios.category.moderation');

if (($session['user']['superuser'] & SU_EDIT_COMMENTS) !== 0)
{
    LotgdNavigation::addNav('bios.nav.moderation', 'moderate.php');
}

LotgdNavigation::addNav('bios.nav.refresh', 'bios.php');

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/bios.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
