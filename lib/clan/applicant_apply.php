<?php

$clanId = (int) \LotgdHttp::getQuery('clanid');

$clanRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class);

if ($clanId > 0)
{
    $charRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
    $mailRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.applicant.apply', [
        'clanOwnerName' => \LotgdSanitize::fullSanitize($params['clanOwnerName'])
    ], $textDomain));

    $session['user']['clanid'] = $clanId;
    $session['user']['clanrank'] = CLAN_APPLICANT;
    $session['user']['clanjoindate'] = new \DateTime('now');

    $subj = ['mail.apply.subject', ['name' => $session['user']['name']], $textDomain];
    $msg = ['mail.apply.message', ['name' => $session['user']['name']], $textDomain];

    $mailRepository->deleteMailFromSystemBySubj(serialize($subj));

    $leaders = $charRepository->getLeadersFromClan($session['user']['clanid'], $session['user']['acctid']);

    foreach($leaders as $leader)
    {
        systemmail($leader['acctid'], $subj, $msg);
    }

    //-- Send reminder mail if clan of choice has a description
    $result = $clanRepository->find($clanId);

    if ('' != trim($result->getClandesc()))
    {
        $subj = ['mail.desc.reminder.subject', [], $textDomain];
        $msg = ['mail.desc.reminder.message', [
            'clanName' => $result->getClanname(),
            'clanShortName' => $result->getClanshort(),
            'description' => $result->getClandesc()
        ], $textDomain];

        systemmail($session['user']['acctid'], $subj, $msg);
    }

    return redirect('clan.php?op=waiting');
}

$order = (int) \LotgdHttp::getQuery('order');

$params['clanList'] = $clanRepository->getClanListWithMembersCount($order);

if (count($params['clanList']))
{
    \LotgdNavigation::addNav('nav.applicant.apply.lobby', 'clan.php');

    \LotgdNavigation::addHeader('category.sorting');
    \LotgdNavigation::addNav('nav.applicant.apply.order.count', 'clan.php?op=apply&order=0');
    \LotgdNavigation::addNav('nav.applicant.apply.order.name', 'clan.php?op=apply&order=1');
}
else
{
    \LotgdNavigation::addNav('nav.applicant.apply.new', 'clan.php?op=new');
    \LotgdNavigation::addNav('nav.applicant.apply.lobby', 'clan.php');
}
