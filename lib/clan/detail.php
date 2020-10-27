<?php

$clanId = (int) \LotgdRequest::getQuery('clanid');

$charRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
$clanRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class);

$params['clanDetail']          = $clanRepository->find($clanId);
$params['SU_AUDIT_MODERATION'] = $session['user']['superuser'] & SU_AUDIT_MODERATION;

page_header('title.detail', ['clanName' => \LotgdSanitize::fullSanitize($params['clanDetail']->getClanname()), 'clanShortName' => $params['clanDetail']->getClanshort()]);

if (($session['user']['superuser'] & SU_AUDIT_MODERATION) && \LotgdRequest::isPost())
{
    $clanName  = \LotgdSanitize::fullSanitize((string) \LotgdRequest::getPost('clanname'));
    $clanShort = \LotgdSanitize::fullSanitize((string) \LotgdRequest::getPost('clanshort'));

    $blockDesc   = \LotgdRequest::getPost('block');
    $unblockDesc = \LotgdRequest::getPost('unblock');

    if ($clanName && $clanShort)
    {
        $params['clanDetail']->setClanname($clanName)
            ->setClanshort($clanShort)
        ;

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('section.detail.superuser.update.clan.names', [], $textDomain));

        LotgdCache::removeItem("clandata-{$clanId}");
    }

    if ($blockDesc)
    {
        $params['clanDetail']->setDescauthor(4294967295)
            ->setClandesc(\LotgdTranslator::t('section.detail.superuser.update.clan.description.reason', [], $textDomain))
        ;

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('section.detail.superuser.update.clan.description.block', [], $textDomain));

        LotgdCache::removeItem("clandata-{$clanId}");
    }
    elseif ($unblockDesc)
    {
        $params['clanDetail']->setDescauthor(0)
            ->setClandesc('')
        ;

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('section.detail.superuser.update.clan.description.unblock', [], $textDomain));

        LotgdCache::removeItem("clandata-{$clanId}");
    }

    \Doctrine::persist($params['clanDetail']);
    \Doctrine::flush();
}

$params['membership'] = $charRepository->getClanMembershipList($clanId);
$params['returnLink'] = \LotgdRequest::getServer('REQUEST_URI');
