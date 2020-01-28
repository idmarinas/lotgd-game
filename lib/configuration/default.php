<?php

$form = \LotgdLocator::get('Lotgd\Core\Form\Configuration');

if (\LotgdHttp::isPost())
{
    $postSettings = \LotgdHttp::getPostAll();
    $form->setData($postSettings);

    $messageType = 'addErrorMessage';
    $message = 'flash.message.error';
    if ($form->isValid())
    {
        $messageType = null;
        $formIsValid = true;
        $rawData = $form->getData();

        $postSettings = [];
        //-- Merge all values, avoid duplicate keys in collections
        foreach($rawData as $key => $value)
        {
            if (is_array($value))
            {
                $postSettings = $postSettings + $value;

                continue;
            }

            $postSettings = $postSettings + [$key => $value];
        }

        require_once 'lib/configuration/save.php';
    }

    if ($messageType)
    {
        \LotgdFlashMessages::{$messageType}(\LotgdTranslator::t($message, [], 'app-form'));
    }
}

$details = gametimedetails();

$secstonewday = secondstonextgameday($details);
$useful_vals = [
    'dayduration' => round(($details['dayduration'] / 60 / 60), 0).' hours',
    'curgametime' => getgametime(),
    'curservertime' => date('Y-m-d h:i:s a'),
    'lastnewday' => date('h:i:s a', strtotime("-{$details['realsecssofartoday']} seconds")),
    'nextnewday' => date('h:i:s a', strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.date('H\\h i\\m s\\s', $secstonewday).')'
];

$form->setData([
    'daysetup' => $useful_vals,
]);

//-- Not set default values if is post request
if(! \LotgdHttp::isPost())
{
    $vals = $settings->getArray() + $useful_vals;

    $data = [
        'game_setup' => $vals,
        'maintenance' => $vals,
        'home' => $vals,
        'beta' => $vals,
        'account' => $vals,
        'commentary' => $vals,
        'places' => $vals,
        'su_title' => $vals,
        'referral' => $vals,
        'events' => $vals,
        'donation' => $vals,
        'training' => $vals,
        'clans' => $vals,
        'newdays' => $vals,
        'forest' => $vals,
        'enemies' => $vals,
        'companion' => $vals,
        'bank' => $vals,
        'mail' => $vals,
        'pvp' => $vals,
        'content' => $vals,
        'logdnet' => $vals,
        'daysetup' => $vals,
        'misc' => $vals,
    ];

    //-- Set values of data base
    $form->setData($data);
}

$params['form'] = $form;
