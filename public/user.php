<?php

//addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/showform.php';
require_once 'lib/datetime.php';
require_once 'lib/sanitize.php';
require_once 'lib/names.php';

check_su_access(SU_EDIT_USERS);

$op = (string) \LotgdHttp::getQuery('op');
$userId = (int) \LotgdHttp::getQuery('userid');
$page = (int) \LotgdHttp::getQuery('page');
$sort = \LotgdHttp::getQuery('sort');
$petition = \LotgdHttp::getQuery('returnpetition');
$m = (string) \LotgdHttp::getQuery('module');

if ('lasthit' == $op)
{
    // Try and keep user editor and captcha from breaking each other.
    $_POST['i_am_a_hack'] = 'true';
}

$textDomain = 'grotto-user';

page_header('title', [], $textDomain);

$repository = \Doctrine::getRepository('LotgdCore:Accounts');

$returnpetition = $petition ? "&returnpetition={$petition}" : '';
$params = [
    'textDomain' => $textDomain,
    'userId' => $userId,
    'returnPetition' => $returnpetition
];

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('user.category.bans');
\LotgdNavigation::addNav('user.nav.ban.add', 'bans.php?op=setupban');
\LotgdNavigation::addNav('user.nav.ban.list', 'bans.php?op=removeban');
\LotgdNavigation::addNav('user.nav.ban.search', 'bans.php?op=searchban');

// Collect a list of the mounts
$mounts = '0,None';
$mountRepository = \Doctrine::getRepository('LotgdCore:Mounts');
$result = $mountRepository->findBy([], [ 'mountcategory' => 'ASC']);

foreach ($result as $row)
{
    $mounts .= ",{$row->getMountid()},{$row->getMountcategory()}: ".\LotgdSanitize::fullSanitize($row->getMountname());
}

$specialties = ['' => 'Undecided'];
$specialties = modulehook('specialtynames', $specialties);
$enum = '';

foreach ($specialties as $key => $name)
{
    $enum .= ($enum ? ',' : '')."{$key},{$name}";
}

//Inserted for v1.1.0 Dragonprime Edition to extend clan possibilities
$ranks = [
    CLAN_APPLICANT => 'ranks.00',
    CLAN_MEMBER => 'ranks.010',
    CLAN_OFFICER => 'ranks.020',
    CLAN_ADMINISTRATIVE => 'ranks.025',
    CLAN_LEADER => 'ranks.030',
    CLAN_FOUNDER => 'ranks.031'
];
$ranksResult = modulehook('clanranks', ['ranks' => $ranks, 'textDomain' => 'page-clan', 'clanid' => null, 'userid' => $userId]);
$ranks = $ranksResult['ranks'];

$rankstring = '';

foreach ($ranks as $rankId => $rankName)
{
    $rankstring .= ($rankstring ? ',' : '').$rankId.','.\LotgdSanitize::fullSanitize(\LotgdTranslator::t($rankName, [], $ranksResult['textDomain']));
}

$races = modulehook('racenames');
$racesenum = '';
//all races here expect such ones no module covers, so we add the users race first.
if ('edit' == $op || 'save' == $op)
{
    //add the race
    $row = $repository->extractEntity($repository->find($userId));
    $row = array_merge($row, $repository->extractEntity($row['character']));
    $racesenum = ','.translate_inline('Undecided', 'race').',';

    foreach ($races as $key => $race)
    {
        $racesenum .= $key.','.$race.',';
    }

    if (! \array_key_exists($row['race'], $races))
    {
        $racesenum .= $row['race'].','.$row['race'].',';
    }
    $racesenum = substr($racesenum, 0, strlen($racesenum) - 1);
    //later on: enumpretrans, because races are already translated in a way...
}

$userinfo = include_once 'lib/data/user_account.php';
$clanRepository = \Doctrine::getRepository('LotgdCore:Clans');
$result = $clanRepository->findAll();

foreach ($result as $clan)
{
    $userinfo['clanid'] .= ",{$clan->getClanid()},".str_replace(',', ';', "<{$clan->getClanshort()}> {$clan->getClanname()}");
}

if ('del' == $op)
{
    require_once 'lib/charcleanup.php';

    $account = $repository->find($userId);

    if ($account)
    {
        if ($account->getSuperuser() > 0 && SU_MEGAUSER != ($session['user']['superuser'] & SU_MEGAUSER))
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.account.del.fail', [], $textDomain));

            return redirect('user.php');
        }

        if (char_cleanup($userId, CHAR_DELETE_MANUAL))
        {
            addnews('news.account.delete', ['playerName' => $account->getCharacter()->getName()], $textDomain, true);

            debuglog("Deleted account {$account->getCharacter()->getName()}");
        }
    }
}
elseif ('lasthit' == $op)
{
    $outputRepository = \Doctrine::getRepository('LotgdCore:AccountsOutput');
    $output2 = $outputRepository->getOutput($userId);

    if ('' == $output2)
    {
        $output2 = \LotgdLocator::build(\Lotgd\Core\Output\Collector::class);
        $text = 'This user has had his navs fixed OR has an empty page stored. Nothing can be displayed to you -_-';
        $output2 = "<html><head><link href=\"templates/common/colors.css\" rel=\"stylesheet\" type=\"text/css\"></head><body style='background-color: #000000; color:red;'>$text</body></html>";
    }
    else
    {
        $output2 = gzuncompress($output2);
    }
    echo str_replace('.focus();', '.blur();', str_replace('<iframe src=', '<iframe Xsrc=', $output2));

    exit(0);
}
elseif ('special' == $op)
{
    $accountEntity = $repository->find($userId);

    if ('' != \LotgdHttp::getPost('newday'))
    {
        $accountEntity->setLasthit(new \DateTime('0000-00-00 00:00:00'));
    }
    elseif ('' != \LotgdHttp::getPost('fixnavs'))
    {
        $character = $accountEntity->getCharacter();
        $character->setAllowednavs([])
            ->setSpecialinc('')
        ;
        $accountEntity->setCharacter($character);

        \Doctrine::persist($character);
        $outputRepository = \Doctrine::getRepository('LotgdCore:AccountsOutput');
        $outputRepository->deleteOutputOfAccount($userId);
    }
    elseif ('' != \LotgdHttp::getPost('clearvalidation'))
    {
        $accountEntity->setEmailvalidation('');
    }

    \Doctrine::persist($accountEntity);
    \Doctrine::flush();

    return redirect("user.php?op=edit&userid={$userId}");
}
elseif ('save' == $op)
{
    $oldValues = $repository->getUserById($userId);
    $postValues = \LotgdHttp::getPostAll();

    $messages = '';
    foreach ($postValues as $key => $val)
    {
        if ('name' == $key)
        {
            continue;
        } //well, name is composed now
        elseif ('newpassword' == $key)
        {
            $postValues['password'] = md5(md5($val));

            continue;
        }
        elseif ('superuser' == $key)
        {
            $value = 0;

            while (list($k, $v) = each($val))
            {
                if ($v)
                {
                    $value += (int) $k;
                }
            }
            //strip off an attempt to set privs that the user doesn't
            //have authority to set.
            $stripfield = ((int) $oldvalues['superuser'] | $session['user']['superuser'] | SU_ANYONE_CAN_SET | ($session['user']['superuser'] & SU_MEGAUSER ? 0xFFFFFFFF : 0));
            $value = $value & $stripfield;
            //put back on privs that the user used to have but the
            //current user can't set.
            $unremovable = ~((int) $session['user']['superuser'] | SU_ANYONE_CAN_SET | ($session['user']['superuser'] & SU_MEGAUSER ? 0xFFFFFFFF : 0));
            $filteredunremovable = (int) $oldvalues['superuser'] & $unremovable;
            $value = $value | $filteredunremovable;

            $postValues[$key] = $value;
            $val = ($value);
        }

        if ($oldValues[$key] != $val)
        {
            $messages .= \LotgdTranslator::t('flash.message.account.edit.changed', [ 'key' => $key, 'oldVal' => $oldValues[$key], 'newVal' => $val ], $textDomain) . '<br>';

            debuglog($session['user']['name']."`0 changed $key from {$oldValues[$key]} to $val", $userId);
        }
    }

    $characterRepository = \Doctrine::getRepository('LotgdCore:Characters');

    $accountEntity = $repository->find($userId);
    $characterEntity = $characterRepository->find($accountEntity->getCharacter()->getId());

    $accountEntity = $repository->hydrateEntity($postValues, $accountEntity);
    $characterEntity = $characterRepository->hydrateEntity($postValues, $characterEntity);

    \Doctrine::persist($accountEntity);
    \Doctrine::persist($characterEntity);
    \Doctrine::flush();

    if ($session['user']['acctid'] == $userId)
    {
        $session['user'] = $repository->getUserById($userId);
    }

    ($messages) && \LotgdFlashMessages::addSuccessMessage($messages);
}
elseif ('savemodule' == $op)
{
    $post = \LotgdHttp::getPostAll();
    $post = modulehook('validateprefs', $post, true, $module);

    if (isset($post['validation_error']) && $post['validation_error'])
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.account.edit.module.error', [ 'error' => $post['validation_error'] ], $textDomain));
    }
    else
    {
        reset($post);

        $userPrefsRepository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');

        $messages = '';
        foreach ($post as $key => $val)
        {
            $entity = $userPrefsRepository->findOneBy([ 'modulename' => $module, 'setting' => $key, 'userid' => $userId ]);

            \Doctrine::persist($userPrefsRepository->hydrateEntity([
                'modulename' => $module,
                'setting' => $key,
                'userid' => $userId,
                'value' => $val
            ], $entity));
            $messages .= \LotgdTranslator::t('flash.message.account.edit.module.setting', [ 'key' => $module, 'val' => $val ], $textDomain);
        }

        \Doctrine::flush();

        $messages .= \LotgdTranslator::t('flash.message.account.edit.module.saved', [ 'module' => $module ], $textDomain);

        \LotgdFlashMessages::addSuccessMessage($messages);
    }

    $op = 'edit';
    \LotgdHttp::setQuery('op', 'edit');
    \LotgdHttp::setQuery('subop', 'module');
}

switch ($op)
{
    case 'edit':
        $params['tpl'] = 'edit';

        $subop = (string) \LotgdHttp::getQuery('subop');

        if ('' != $petition)
        {
            \LotgdNavigation::addHeader('common.category.navigation');
            \LotgdNavigation::addNav('user.nav.petition', "viewpetition.php?op=view&id=$petition");
        }

        \LotgdNavigation::addHeader('user.category.operations');
        \LotgdNavigation::addNav('user.nav.last.hit', "user.php?op=lasthit&userid={$userId}");
        \LotgdNavigation::addNav('user.nav.debuglog', "user.php?op=debuglog&userid={$userId}{$returnpetition}");
        \LotgdNavigation::addNav('user.nav.bio', "bio.php?char={$userId}&ret=".urlencode(\LotgdHttp::getServer('REQUEST_URI')));

        if ($session['user']['superuser'] & SU_EDIT_DONATIONS)
        {
            \LotgdNavigation::addNav('user.nav.donation', 'donators.php?op=add1&name='.rawurlencode($row['login']).'&ret='.urlencode(\LotgdHttp::getServer('REQUEST_URI')));
        }

        \LotgdNavigation::addHeader('user.category.bans');
        \LotgdNavigation::addNav('user.nav.ban.setup', "bans.php?op=setupban&userid={$userId}");

        module_editor_navs('prefs', "user.php?op=edit&subop=module&userid={$userId}{$returnpetition}&module=");

        if ('' == $subop)
        {
            $row = $repository->extractEntity($repository->find($userId));
            $row = array_merge($row, $repository->extractEntity($row['character']));

            $params['userLoggedIn'] = $row['loggedin'];

            //Add new composita attack
            $row['totalattack'] = get_player_attack($row['acctid']);
            $row['totaldefense'] = get_player_defense($row['acctid']);

            //Add the count summary for DKs
            $row['dragonpointssummary'] = [];

            if ($row['dragonkills'])
            {
                $row['dragonpointssummary'] = array_count_values($row['dragonpoints']);
            }

            $showformargs = modulehook('modifyuserview', ['userinfo' => $userinfo, 'user' => $row]);
            $params['form'] = lotgd_showform($showformargs['userinfo'], $showformargs['user'], true, false, false);
        }
        elseif ('module' == $subop)
        {
            $module = (string) \LotgdHttp::getQuery('module');

            \LotgdNavigation::addHeader('user.category.operations');
            \LotgdNavigation::addNav('user.nav.edit', "user.php?op=edit&userid={$userId}{$returnpetition}");

            $info = get_module_info($module);

            if (count($info['prefs']) > 0)
            {
                $data = [];
                $msettings = [];

                foreach ($info['prefs'] as $key => $val)
                {
                    // Handle vals which are arrays.
                    if (is_array($val))
                    {
                        $v = $val[0];
                        $x = explode('|', $v);
                        $val[0] = $x[0];
                        $x[0] = $val;
                    }
                    else
                    {
                        $x = explode('|', $val);
                    }
                    $msettings[$key] = $x[0];
                    // Set up the defaults as well.
                    if (isset($x[1]))
                    {
                        $data[$key] = $x[1];
                    }
                }

                $userPrefsRepository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
                $result = $userPrefsRepository->findBy(['modulename' => $module, 'userid' => $userId]);

                foreach ($result as $row)
                {
                    $data[$row->getSetting()] = $row->getValue();
                }

                rawoutput("<form action='user.php?op=savemodule&module=$module&userid=$userid$returnpetition' method='POST'>");
                \LotgdNavigation::addNavAllow("user.php?op=savemodule&module=$module&userid=$userid$returnpetition");
                lotgd_showform($msettings, $data);
                rawoutput('</form>');

                page_footer();
            }
            else
            {
                \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.account.edit.no.prefs', ['module' => $module], $textDomain));

                return redirect("user.php?op=edit&userid={$userId}{$returnpetition}");
            }
        }
    break;
    case 'debuglog':
        $params['tpl'] = 'debuglog';

        \LotgdNavigation::addHeader('user.category.operations');
        \LotgdNavigation::addNav('user.nav.edit', "user.php?op=edit&userid={$userId}{$returnpetition}");
        \LotgdNavigation::addNav('user.nav.refresh', "user.php?op=debuglog&userid={$userId}{$returnpetition}");

        $debugLogRepository = \Doctrine::getRepository('LotgdCore:Debuglog');

        $query = $debugLogRepository->createQueryBuilder('u');

        $query->select('u.id', 'u.date', 'u.actor', 'u.target', 'u.message', 'u.field', 'u.value')
            ->addSelect('c.name AS actorName', 'c2.name AS targetName')
            ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $query->expr()->eq('c.acct', 'u.actor'))
            ->leftJoin('LotgdCore:Characters', 'c2', 'WITH', $query->expr()->eq('c2.acct', 'u.target'))
            ->where('u.actor = :acct OR u.target = :acct')

            ->orderBy('u.date', 'DESC')

            ->setParameter('acct', $userId)
        ;

        $params['paginator'] = $debugLogRepository->getPaginator($query, $page, 500);
    break;
    default:
        $params['tpl'] = 'default';

        $repoAcctEveryPage = \Doctrine::getRepository('LotgdCore:AccountsEverypage');

        $page = (int) \LotgdHttp::getQuery('page');
        $sort = (string) \LotgdHttp::getQuery('sort');
        $order = (string) ($sort ?: 'acctid');

        $query = (string) \LotgdHttp::getPost('q');
        $query = (string) ($query ?: \LotgdHttp::getQuery('q'));

        $params['query'] = $query ? "q={$query}" : '';
        $params['paginator'] = $repository->userSearchAccounts($query, $order, $page);
        $params['stats'] = $repoAcctEveryPage->getStatsPageGen();

        $params['paginatorLink'] = \LotgdHttp::getServer('REQUEST_URI');
    break;
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/user.twig', $params));

page_footer();

function show_bitfield($val)
{
    $out = '';
    $v = 1;

    for ($i = 0; $i < 32; $i++)
    {
        $out .= (int) $val & (int) $v ? '1' : '0';
        $v *= 2;
    }

    return $out;
}
