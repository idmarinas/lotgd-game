<?php

// translator ready
// addnews ready
// mail ready
$thispage_superuser_level = 0;
function check_su_access($level)
{
    global $session,$thispage_superuser_level;

    $thispage_superuser_level = $thispage_superuser_level | $level;

    $textDomain = 'partial-access';

    rawoutput('<!--Su_Restricted-->');

    if ($session['user']['superuser'] & $level)
    {
        //-- They have appropriate levels, let's see if there's a module that restricts access beyond this point.
        $return = modulehook('check_su_access', ['enabled' => true, 'level' => $level]);

        if ($return['enabled'])
        {
            $session['user']['laston'] = new \DateTime('now');

            return;
        }

        page_header('title.ops', [], $textDomain);

        rawoutput(LotgdTheme::renderLotgdTemplate('core/partial/access/ops.twig', [ 'textDomain' => $textDomain ]));

        \LotgdNavigation::addNav('common.superuser.mundane', 'village.php');

        page_footer();
    }

    clearnav();

    // This buff is useless because the graveyard (rightly, really)
    // wipes all buffs when you enter it.  This means that you never really
    // have this effect unless you log out without going to the graveyard
    // for some odd reason.
    //		apply_buff('angrygods',
    //			array(
    //				"name"=>"`^The gods are angry!",
    //				"rounds"=>10,
    //				"wearoff"=>"`^The gods have grown bored with teasing you.",
    //				"minioncount"=>$session['user']['level'],
    //				"maxgoodguydamage"=> 2,
    //				"effectmsg"=>"`7The gods curse you, causing `\${damage}`7 damage!",
    //				"effectnodmgmsg"=>"`7The gods have elected not to tease you just now.",
    //				"allowinpvp"=>1,
    //				"survivenewday"=>1,
    //				"newdaymessage"=>"`6The gods are still angry with you!",
    //				"schema"=>"superuser",
    //				)
    //		);


    $session['output'] = '';

    page_header('title.infidel', [], $textDomain);

    addnews('`&%s was smitten down for attempting to defile the gods (they tried to hack superuser pages).', $session['user']['name']);

    debuglog("Lost {$session['user']['gold']} and ".($session['user']['experience'] * 0.25).' experience trying to hack superuser pages.');

    $session['user']['hitpoints'] = 0;
    $session['user']['alive'] = 0;
    $session['user']['soulpoints'] = 0;
    $session['user']['gravefights'] = 0;
    $session['user']['deathpower'] = 0;
    $session['user']['gold'] = 0;
    $session['user']['experience'] *= 0.75;

    \LotgdNavigation::addNav('home.nav.news', 'news.php');

    $repository = \Doctrine::getRepository('LotgdCore:Accounts');
    $result = $repository->getSuperuserWithPermit(SU_EDIT_USERS);

    require_once 'lib/systemmail.php';

    foreach ($result as $row)
    {
        $subj = [
            'mail.subject',
            [ 'name' => $session['user']['name'] ],
            $textDomain
        ];
        $body = [
            'mail.message',
            [
                'name' => $session['user']['name'],
                'uri' => \LotgdHttp::getServer('REQUEST_URI'),
                'referer' => \LotgdHttp::getServer('HTTP_REFERER')
            ],
            $textDomain
        ];

        systemmail($row['acctid'], $subj, $body);
    }

    rawoutput(LotgdTheme::renderLotgdTemplate('core/partial/access/infidel.twig', [
        'textDomain' => $textDomain,
        'deathOverlord' => getsetting('deathoverlord', '`$Ramius')
    ]));

    page_footer();
}

/**
 * Check Superuser premission.
 * Just check and redirect if denied you have permission.
 *
 * @param int    $permission
 * @param string $return
 */
function checkSuPermission($permission, string $return)
{
    global $session;

    if ($session['user']['superuser'] & $permission)
    {
        $result = modulehook('check-su-permission', ['enabled' => true, 'permission' => $permission]);

        if ($result['enabled'])
        {
            $session['user']['laston'] = new \DateTime('now');

            return null;
        }

        //-- Module preventing doing
        return redirect($return);
    }

    return redirect($return);
}
