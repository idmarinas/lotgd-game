<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/systemmail.php';

check_su_access(SU_EDIT_COMMENTS);

tlschema('bio');

$textDomain = 'page-bios';

page_header('title', [], $textDomain);

$op = (string) \LotgdHttp::getQuery('op');
$userid = (int) \LotgdHttp::getQuery('charid');

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);

$params = [
    'textDomain' => $textDomain
];

if ('block' == $op)
{
    $message = 'player.block.fail';
    $flashMessage = 'addErrorMessage';

    if ($repository->blockCharacterBio($userid))
    {
        $message = 'player.block.success';
        $flashMessage = 'addSuccessMessage';

        $subj = ['mail.block.subject', [], $textDomain];
        $msg = ['mail.block.message', [], $textDomain];

        systemmail($userid, $subj, $msg);
    }

    \LotgdFlashMessages::{$flashMessage}(\LotgdTranslator::t($message, [], $textDomain));
}
elseif ('unblock' == $op)
{
    $message = 'player.unblock.fail';
    $flashMessage = 'addErrorMessage';

    if ($repository->unblockCharacterBio($userid))
    {
        $message = 'player.unblock.success';
        $flashMessage = 'addSuccessMessage';

        $subj = ['mail.unblock.subject', [], $textDomain];
        $msg = ['mail.unblock.message', [], $textDomain];

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

//-- This is only for params not use for other purpose
$params = modulehook('page-bios-tpl-params', $params);
rawoutput(LotgdTheme::renderThemeTemplate('page/bios.twig', $params));

DB::free_result($result);
page_footer();
