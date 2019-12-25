<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/systemmail.php';

check_su_access(SU_EDIT_COMMENTS);

$textDomain = 'page-bios';

page_header('title', [], $textDomain);

$op = (string) \LotgdHttp::getQuery('op');
$userid = (int) \LotgdHttp::getQuery('charid');

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);

$params = [
    'textDomain' => $textDomain
];

if ('block' == $op || 'unblock' == $op)
{
    $message = "player.{$op}.fail";
    $flashMessage = 'addErrorMessage';

    if ($repository->blockCharacterBio($userid))
    {
        $message = "player.{$op}.success";
        $flashMessage = 'addSuccessMessage';

        $subj = ["mail.{$op}.subject", [], $textDomain];
        $msg = ["mail.{$op}.message", [], $textDomain];

        systemmail($userid, $subj, $msg);
    }

    \LotgdFlashMessages::{$flashMessage}(\LotgdTranslator::t($message, [], $textDomain));
}

$params['unblocked'] = $repository->getCharactersUnblockedBio();
$params['blocked'] = $repository->getCharactersBlockedBio();

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addNav('bios.category.moderation');

if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
{
    \LotgdNavigation::addNav('bios.nav.moderation', 'moderate.php');
}

\LotgdNavigation::addNav('bios.nav.refresh', 'bios.php');

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/bios.twig', $params));

page_footer();
