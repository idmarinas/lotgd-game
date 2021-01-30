<?php

$params['clanShortNameLength'] = getsetting('clanshortnamelength', 5);

$lotgdFormFactory = \LotgdKernel::get('form.factory');

$entity = new \Lotgd\Core\Entity\Clans();
$form = $lotgdFormFactory->create(\Lotgd\Core\Form\ClanNewType::class, $entity, [
    'action' => 'clan.php?op=new',
    'attr'   => [
        'autocomplete' => 'off',
    ],
]);

$form->handleRequest(\LotgdRequest::_i());

$params['clan_created'] = false;
//-- Not have money
if ($session['user']['gold'] < $costGold || $session['user']['gems'] < $costGems)
{
    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('section.applicant.new.form.validator.no.fees', $params, $textDomain));
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('section.applicant.new.form.validator.denied', $params, $textDomain));
}
elseif ($form->isSubmitted() && $form->isValid())
{
    $args = ['clanname' => $entity->getClanname(), 'clanshort' => $entity->getClanshort()];

    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CLAN_CREATE, null, $args);
    $args = modulehook('process-createclan', $args);

    if ($args['blocked'] ?? false)
    {
        \LotgdFlashMessages::addWarningMessage($args['blockmsg']);
    }
    else
    {
        \Doctrine::persist($entity);
        \Doctrine::flush();

        $params['clanName']  = $entity->getClanname();
        $params['clanShort'] = $entity->getClanshort();

        $session['user']['clanid']       = $entity->getClanid();
        $session['user']['clanrank']     = CLAN_FOUNDER;
        $session['user']['clanjoindate'] = new \DateTime('now');
        $session['user']['gold'] -= $costGold;
        $session['user']['gems'] -= $costGems;

        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('section.applicant.new.form.success', $params, $textDomain));

        debuglog("has started a new clan (<{$entity->getClanshort()}> {$entity->getClanname()}) for {$costGold} gold and {$costGems} gems.");

        $params['clan_created'] = true;
    }
}

$params['form'] = $form->createView();

\LotgdNavigation::addNav('nav.applicant.apply.lobby', 'clan.php');
