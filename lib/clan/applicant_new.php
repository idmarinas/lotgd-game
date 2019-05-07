<?php

$apply = (int) \LotgdHttp::getQuery('apply');

$params['clanShortNameLength'] = getsetting('clanshortnamelength', 5);

if (1 == $apply)
{
    $clanNameOriginal = (string) \LotgdHttp::getPost('clanname');
    $clanShortOriginal = (string) \LotgdHttp::getPost('clanshort');

    //-- Validate clan name
    $chainNameValidator = new \Zend\Validator\ValidatorChain();

    //-- Check if clan name is taken
    $noExistValidator = new \Lotgd\Core\Validator\Db\NoObjectExists ([
        'object_repository' => \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class),
        'fields'   => 'clanname',
    ]);
    $noExistValidator->setMessage(
        \LotgdTranslator::t('section.applicant.new.form.validator.clan.name.exist', $params, $textDomain),
        \Lotgd\Core\Validator\Db\NoObjectExists::ERROR_OBJECT_FOUND
    );

    //-- Check length of clan name
    $nameLength = new \Zend\Validator\StringLength(['min' => 2, 'max' => 250]);
    $nameLength->setMessages([
        \Zend\Validator\StringLength::TOO_SHORT => \LotgdTranslator::t('section.applicant.new.form.validator.clan.name.length.short', $params, $textDomain),
        \Zend\Validator\StringLength::TOO_LONG => \LotgdTranslator::t('section.applicant.new.form.validator.clan.name.length.long', $params, $textDomain)
    ]);

    $chainNameValidator->attach($nameLength)
        ->attach($noExistValidator)
    ;

    //-- Validate clan short name
    $chainShortValidator = new \Zend\Validator\ValidatorChain();

    //-- Check if clan short name is taken
    $noExistValidator = new \Lotgd\Core\Validator\Db\NoObjectExists ([
        'object_repository' => \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class),
        'fields'   => 'clanshort',
    ]);
    $noExistValidator->setMessage(
        \LotgdTranslator::t('section.applicant.new.form.validator.clan.short.exist', $params, $textDomain),
        \Lotgd\Core\Validator\Db\NoObjectExists::ERROR_OBJECT_FOUND
    );

    //-- Check length of clan short name
    $nameLength = new \Zend\Validator\StringLength(['min' => 2, 'max' => $params['clanShortNameLength']]);
    $nameLength->setMessages([
        \Zend\Validator\StringLength::TOO_SHORT => \LotgdTranslator::t('section.applicant.new.form.validator.clan.short.length.short', $params, $textDomain),
        \Zend\Validator\StringLength::TOO_LONG => \LotgdTranslator::t('section.applicant.new.form.validator.clan.short.length.long', $params, $textDomain)
    ]);

    $chainShortValidator->attach(new \Zend\Validator\StringLength(['min' => 2, 'max' => $params['clanShortNameLength']]))
        ->attach($noExistValidator)
    ;

    $clanNameFilter = preg_replace("/[^\p{L} '-]/", '', $clanNameOriginal);
    $clanShortFilter = preg_replace('/[^[:alpha:]]/', '', $clanShortOriginal);

    //-- Contain unallowed characters
    if ($clanNameFilter != $clanNameOriginal || $clanShortFilter != $clanShortOriginal)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('section.applicant.new.form.validator.invalid.letters', $params, $textDomain));

        return redirect('clan.php?op=new');
    }
    //-- Fail validators
    elseif (! $chainShortValidator->isValid($clanShortOriginal) || ! $chainNameValidator->isValid($clanNameOriginal))
    {
        foreach($chainShortValidator->getMessages() as $message)
        {
            \LotgdFlashMessages::addWarningMessage($message);
        }

        foreach($chainNameValidator->getMessages() as $message)
        {
            \LotgdFlashMessages::addWarningMessage($message);
        }

        return redirect('clan.php?op=new');
    }

    if ($session['user']['gold'] < $gold || $session['user']['gems'] < $gems)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('section.applicant.new.form.validator.no.fees', $params, $textDomain));
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('section.applicant.new.form.validator.denied', $params, $textDomain));

        return redirect('clan.php');
    }

    $args = ['ocn' => $clanNameOriginal, 'ocs' => $clanShortOriginal, 'clanname' => $clanNameFilter, 'clanshort' => $clanShortFilter];

    $args = modulehook('process-createclan', $args);

    if ($args['blocked'] ?? false)
    {
        \LotgdFlashMessages::addWarningMessage($args['blockmsg']);

        return redirect('clan.php?op=new');
    }

    $clanRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class);

    $clanId = $clanRepository->createClan([
        'clanname' => $clanNameFilter,
        'clanshort' => $clanShortFilter
    ]);

    $params['clanName'] = $clanNameFilter;
    $params['clanShort'] = $clanShortFilter;

    if ($clanId)
    {
        $session['user']['clanid'] = $clanId;
        $session['user']['clanrank'] = CLAN_FOUNDER;
        $session['user']['clanjoindate'] = new \DateTime('now');
        $session['user']['gold'] -= $costGold;
        $session['user']['gems'] -= $costGems;

        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('section.applicant.new.form.success', $params, $textDomain));

        debuglog("has started a new clan (<{$clanShortFilter}> {$clanNameFilter}) for {$costGold} gold and {$costGems} gems.");

        return redirect('clan.php');
    }

    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('section.applicant.new.form.fail', $params, $textDomain));

    return redirect('clan.php?op=new');
}

\LotgdNavigation::addNav('nav.applicant.apply.lobby', 'clan.php');
