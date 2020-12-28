<?php

$type         = (string) \LotgdRequest::getPost('type');
$valueIp      = (string) \LotgdRequest::getPost('ip');
$valueId      = (string) \LotgdRequest::getPost('id');
$durationDays = (int) \LotgdRequest::getPost('duration');
$durationDays = \max(0, $durationDays); //-- Min value is 0
$reason       = (string) \LotgdRequest::getPost('reason');

$duration = new \DateTime('0000-00-00');

if ($durationDays)
{
    $duration = new \DateTime('now');
    $duration->add(new \DateInterval("P{$durationDays}D"));
}

$process = true;

if ('ip' == $type && \substr(\LotgdRequest::getServer('REMOTE_ADDR'), 0, \strlen($valueIp)) == $valueIp)
{
    $process = false;
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('saveban.yourself.ip', [], $textDomain));
}
elseif ('id' == $type && \LotgdRequest::getCookie('lgi') == $valueId)
{
    $process = false;
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('saveban.yourself.id', [], $textDomain));
}

if ($process)
{
    $repository     = \Doctrine::getRepository(\Lotgd\Core\Entity\Bans::class);
    $repositoryAcct = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
    $entity         = new \Lotgd\Core\Entity\Bans();

    $entity->setBanner($session['user']['name'])
        ->setBanreason($reason)
        ->setBanexpire($duration)
    ;

    (('ip' == $type) ? $entity->setIpfilter($valueIp) : $entity->setUniqueid($valueId));

    try
    {
        \Doctrine::merge($entity);
        \Doctrine::flush();

        debuglog(\sprintf(
            'entered a ban: %s. Ends after: %s, Reason: %s.',
            ('ip' == $type ? "IP: {$valueIp}" : "ID: {$valueId}"),
            $duration->format('Y-m-d'),
            $reason
        ));

        $params['savedBan'] = true;
    }
    catch (\Throwable $th)
    {
        \Tracy\Debugger::log($th);

        \LotgdFlashMessages::addErrorMessage($th->getMessage());

        $params['savedBan'] = false;
    }

    $params['logoutCount'] = $repositoryAcct->logoutAffectedAccounts($valueIp, $valueId, $type);
}
