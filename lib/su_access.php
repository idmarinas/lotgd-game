<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Event\Superuser;

$thispage_superuser_level = 0;
function check_su_access($level)
{
    global $session,$thispage_superuser_level;

    $thispage_superuser_level = $thispage_superuser_level | $level;

    $textDomain = 'partial_access';

    \LotgdResponse::pageAddContent('<!--Su_Restricted-->');

    if ($session['user']['superuser'] & $level)
    {
        //-- They have appropriate levels, let's see if there's a module that restricts access beyond this point.

        $return = new Superuser(['enabled' => true, 'level' => $level]);
        \LotgdEventDispatcher::dispatch($return, Superuser::CHECK_SU_ACCESS);
        $return = $return->getData();

        if ($return['enabled'])
        {
            $session['user']['laston'] = new \DateTime('now');

            return;
        }

        \LotgdResponse::pageStart('title.ops', [], $textDomain);

        $tpl = LotgdTheme::load('admin/_blocks/_access.html.twig');
        \LotgdResponse::pageAddContent($tpl->renderBlock('access_ops', ['textDomain' => $textDomain]));

        \LotgdNavigation::addNav('common.superuser.mundane', 'village.php');

        \LotgdResponse::pageEnd();
    }

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

    \LotgdResponse::pageStart('title.infidel', [], $textDomain);

    \LotgdTool::addNews('`&%s was smitten down for attempting to defile the gods (they tried to hack superuser pages).', $session['user']['name']);

    \LotgdLog::debug("Lost {$session['user']['gold']} and ".($session['user']['experience'] * 0.25).' experience trying to hack superuser pages.');

    $session['user']['hitpoints']   = 0;
    $session['user']['alive']       = 0;
    $session['user']['soulpoints']  = 0;
    $session['user']['gravefights'] = 0;
    $session['user']['deathpower']  = 0;
    $session['user']['gold']        = 0;
    $session['user']['experience'] *= 0.75;

    \LotgdNavigation::addNav('home.nav.news', 'news.php');

    $repository = \Doctrine::getRepository('LotgdCore:User');
    $result     = $repository->getSuperuserWithPermit(SU_EDIT_USERS);

    foreach ($result as $row)
    {
        $subj = [
            'mail.subject',
            ['name' => $session['user']['name']],
            $textDomain,
        ];
        $body = [
            'mail.message',
            [
                'name'    => $session['user']['name'],
                'uri'     => \LotgdRequest::getServer('REQUEST_URI'),
                'referer' => \LotgdRequest::getServer('HTTP_REFERER'),
            ],
            $textDomain,
        ];

        LotgdKernel::get('lotgd_core.tool.system_mail')->send($row['acctid'], $subj, $body);
    }

    $tpl = \LotgdTheme::load('admin/_blocks/_access.html.twig');
    \LotgdResponse::pageAddContent($tpl->renderBlock('access_infidel', [
        'textDomain'    => $textDomain,
        'deathOverlord' => LotgdSetting::getSetting('deathoverlord', '`$Ramius'),
    ]));

    \LotgdResponse::pageEnd();
}

/**
 * Check Superuser premission.
 * Just check and redirect if denied you have permission.
 *
 * @param int $permission
 *
 * @return bool|redirect
 */
function checkSuPermission($permission, ?string $return = null)
{
    global $session;

    if ($session['user']['superuser'] & $permission)
    {
        $result = new Superuser(['enabled' => true, 'permission' => $permission]);
        \LotgdEventDispatcher::dispatch($result, Superuser::CHECK_SU_PERMISSION);
        $result = $result->getData();

        if ($result['enabled'])
        {
            $session['user']['laston'] = new \DateTime('now');

            return true;
        }
    }

    if ( ! $return)
    {
        redirect($return);
    }

    return false;
}
