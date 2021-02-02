<?php

use Lotgd\Core\Form\ConfigurationType;

$details = gametimedetails();

$secstonewday = secondstonextgameday($details);
$useful_vals  = [
    'dayduration'   => \round(($details['dayduration'] / 60 / 60), 0).' hours',
    'curgametime'   => getgametime(),
    'curservertime' => \date('Y-m-d h:i:s a'),
    'lastnewday'    => \date('h:i:s a', \strtotime("-{$details['realsecssofartoday']} seconds")),
    'nextnewday'    => \date('h:i:s a', \strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.\date('H\\h i\\m s\\s', $secstonewday).')',
];

$settings = \LotgdKernel::get(Lotgd\Core\Lib\Settings::class);
$vals = \array_merge($settings->getArray(), $useful_vals);

$data = [
    'game_setup'  => $vals,
    'maintenance' => $vals,
    'combat'      => $vals,
    'home'        => $vals,
    'account'     => $vals,
    'commentary'  => $vals,
    'places'      => $vals,
    'su_title'    => $vals,
    'referral'    => $vals,
    'events'      => $vals,
    'donation'    => $vals,
    'training'    => $vals,
    'clans'       => $vals,
    'newdays'     => $vals,
    'forest'      => $vals,
    'enemies'     => $vals,
    'companion'   => $vals,
    'bank'        => $vals,
    'mail'        => $vals,
    'pvp'         => $vals,
    'content'     => $vals,
    'logdnet'     => $vals,
    'daysetup'    => $vals,
    'misc'        => $vals,
];

$lotgdFormFactory = \LotgdKernel::get('form.factory');

$form = $lotgdFormFactory->create(ConfigurationType::class, $data, [
    'action' => 'configuration.php?setting=default&save=save',
    'attr'   => [
        'autocomplete' => 'off',
    ],
]);

\LotgdNavigation::addNavAllow('configuration.php?setting=default&save=save');

$form->handleRequest(\LotgdRequest::_i());

if ($form->isSubmitted() && $form->isValid())
{
    $messageType = null;
    $formIsValid = true;
    $rawData     = $form->getData();

    $postSettings = [];
    //-- Merge all values, avoid duplicate keys in collections
    foreach ($rawData as $key => $value)
    {
        if (\is_array($value))
        {
            $postSettings = $postSettings + $value;

            continue;
        }

        $postSettings = $postSettings + [$key => $value];
    }

    require_once 'lib/configuration/save.php';

    if ($messageType)
    {
        \LotgdFlashMessages::{$messageType}(\LotgdTranslator::t($message, [], 'form_app'));
    }
}

$params['form'] = $form->createView();
