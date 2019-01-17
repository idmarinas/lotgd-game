<?php

// translator ready
// addnews ready
// mail ready

function saveuser()
{
    global $session, $companions, $chatloc;

    //-- It's defined as not save user, Not are a user logged in or not are defined id of account
    if (defined('NO_SAVE_USER') || ! ($session['user']['loggedin'] ?? false) || ! ($session['user']['acctid'] ?? false))
    {
        return false;
    }

    // Any time we go to save a user, make SURE that any tempstat changes
    // are undone.
    restore_buff_fields();

    if (! $chatloc)
    {
        $session['user']['chatloc'] = 0;
    }

    $session['user']['bufflist'] = $session['bufflist'];
    $session['user']['laston'] = new DateTime('now');

    if (isset($companions) && is_array($companions))
    {
        $session['user']['companions'] = $companions;
    }

    $hydrator = new \Zend\Hydrator\ClassMethods();

    $everypage = $hydrator->hydrate($session['user'], new \Lotgd\Core\Entity\AccountsEverypage());

    $account = $hydrator->hydrate($session['user'], new \Lotgd\Core\Entity\Accounts());

    $character = $hydrator->hydrate($session['user'], new \Lotgd\Core\Entity\Characters());

    $account->setCharacter($character);
    $character->setAcct($account);

    \Doctrine::merge($account);
    \Doctrine::merge($character);
    \Doctrine::merge($everypage);

    if ($session['output'] ?? false)
    {
        $acctOutput = new \Lotgd\Core\Entity\AccountsOutput();
        $acctOutput->setAcctid($session['user']['acctid'])
            ->setOutput(gzcompress($session['output'], 1))
        ;

        \Doctrine::merge($acctOutput);
    }

    \Doctrine::flush(); //Persist objects
    \Doctrine::clear();//-- Detaches all objects from Doctrine!

    $session['user'] = [
        'acctid' => $session['user']['acctid'],
        'login' => $session['user']['login']
    ];
}
