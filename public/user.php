<?php

use Lotgd\Core\EntityForm\AccountsType;
use Lotgd\Core\EntityForm\CharactersType;
//addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_USERS);

$op       = (string) LotgdRequest::getQuery('op');
$userId   = (int) LotgdRequest::getQuery('userid');
$page     = (int) LotgdRequest::getQuery('page');
$sort     = LotgdRequest::getQuery('sort');
$petition = LotgdRequest::getQuery('returnpetition');
$module   = (string) LotgdRequest::getQuery('module');

if ('lasthit' == $op)
{
    // Try and keep user editor and captcha from breaking each other.
    $_POST['i_am_a_hack'] = 'true';
}

$textDomain = 'grotto_user';

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$repository = Doctrine::getRepository('LotgdCore:User');

$returnpetition = $petition ? "&returnpetition={$petition}" : '';
$params         = [
    'textDomain'     => $textDomain,
    'userId'         => $userId,
    'returnPetition' => $returnpetition,
];

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addHeader('user.category.bans');
LotgdNavigation::addNav('user.nav.ban.add', 'bans.php?op=setupban');
LotgdNavigation::addNav('user.nav.ban.list', 'bans.php?op=removeban');
LotgdNavigation::addNav('user.nav.ban.search', 'bans.php?op=searchban');

//all races here expect such ones no module covers, so we add the users race first.
if ('edit' == $op)
{
    //add the race
    $row           = $repository->extractEntity($repository->find($userId));
    $characterInfo = $row;
    $row           = array_merge($row, $repository->extractEntity($row['character']));
}

if ('del' == $op)
{
    $account = $repository->find($userId);

    if ($account)
    {
        if ($account->getSuperuser() > 0 && SU_MEGAUSER != ($session['user']['superuser'] & SU_MEGAUSER))
        {
            LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.account.del.fail', [], $textDomain));

            redirect('user.php');
        }

        if (LotgdKernel::get('lotgd.core.backup')->characterCleanUp($userId, CHAR_DELETE_MANUAL))
        {
            LotgdTool::addNews('news.account.delete', ['playerName' => $account->getAvatar()->getName()], $textDomain, true);

            LotgdLog::debug("Deleted account {$account->getAvatar()->getName()}");
        }
    }
}
elseif ('lasthit' == $op)
{
    $outputRepository = Doctrine::getRepository('LotgdCore:AccountsOutput');
    $output2          = $outputRepository->getOutput($userId);

    if ('' == $output2)
    {
        $text    = 'This user has had his navs fixed OR has an empty page stored. Nothing can be displayed to you -_-';
        $output2 = "<html><head><link href=\"templates/common/colors.css\" rel=\"stylesheet\" type=\"text/css\"></head><body style='background-color: #000000; color:red;'>{$text}</body></html>";
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

    if ('' != LotgdRequest::getPost('newday'))
    {
        $character = $accountEntity->getAvatar();
        $character->setLasthit(new DateTime('0000-00-00 00:00:00'));
        $accountEntity->setAvatar($character);

        Doctrine::persist($character);
    }
    elseif ('' != LotgdRequest::getPost('fixnavs'))
    {
        $character = $accountEntity->getAvatar();
        $character->setAllowednavs([])
            ->setSpecialinc('')
        ;
        $accountEntity->setAvatar($character);

        Doctrine::persist($character);
        $outputRepository = Doctrine::getRepository('LotgdCore:AccountsOutput');
        $outputRepository->deleteOutputOfAccount($userId);
    }
    elseif ('' != LotgdRequest::getPost('clearvalidation'))
    {
        $accountEntity->setEmailvalidation('');
    }

    Doctrine::persist($accountEntity);
    Doctrine::flush();

    redirect("user.php?op=edit&userid={$userId}");
}
elseif ('save' == $op)
{
    $oldValues  = $repository->getUserById($userId);
    $postValues = LotgdRequest::getPostAll();

    $messages = '';

    foreach ($postValues as $key => $val)
    {
        if ('name' == $key)
        {
            continue;
        } //well, name is composed now
        elseif ('newpassword' == $key)
        {
            /** @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoder $passwordEncoder */
            $passwordEncoder        = LotgdKernel::get('security.password_encoder');
            $postValues['password'] = $passwordEncoder->encodePassword($repository->find($userId), $val);

            continue;
        }
        elseif ('superuser' == $key)
        {
            $value = 0;

            foreach ($val as $k => $v)
            {
                if ($v)
                {
                    $value += (int) $k;
                }
            }
            //strip off an attempt to set privs that the user doesn't
            //have authority to set.
            $stripfield = ((int) $oldvalues['superuser'] | $session['user']['superuser'] | SU_ANYONE_CAN_SET | (($session['user']['superuser'] & SU_MEGAUSER) !== 0 ? 0xFFFFFFFF : 0));
            $value &= $stripfield;
            //put back on privs that the user used to have but the
            //current user can't set.
            $unremovable         = ~((int) $session['user']['superuser'] | SU_ANYONE_CAN_SET | (($session['user']['superuser'] & SU_MEGAUSER) !== 0 ? 0xFFFFFFFF : 0));
            $filteredunremovable = (int) $oldvalues['superuser'] & $unremovable;
            $value |= $filteredunremovable;

            $postValues[$key] = $value;
            $val              = ($value);
        }

        if ($oldValues[$key] != $val)
        {
            $messages .= LotgdTranslator::t('flash.message.account.edit.changed', ['key' => $key, 'oldVal' => $oldValues[$key], 'newVal' => $val], $textDomain).'<br>';

            LotgdLog::debug($session['user']['name']."`0 changed {$key} from {$oldValues[$key]} to {$val}", $userId);
        }
    }

    $characterRepository = Doctrine::getRepository('LotgdCore:Avatar');

    $accountEntity   = $repository->find($userId);
    $characterEntity = $characterRepository->find($accountEntity->getAvatar()->getId());

    $accountEntity   = $repository->hydrateEntity($postValues, $accountEntity);
    $characterEntity = $characterRepository->hydrateEntity($postValues, $characterEntity);

    Doctrine::persist($accountEntity);
    Doctrine::persist($characterEntity);
    Doctrine::flush();

    if ($session['user']['acctid'] == $userId)
    {
        $session['user'] = $repository->getUserById($userId);
    }

    ($messages) && LotgdFlashMessages::addSuccessMessage($messages);
}
elseif ('savemodule' == $op)
{
    $post = LotgdRequest::getPostAll();
    $args = new GenericEvent(null, $post);
    LotgdEventDispatcher::dispatch($args, Events::PAGE_USER_VALIDATE_PREFS);
    $post = modulehook('validateprefs', $args->getArguments(), true, $module);

    if (isset($post['validation_error']) && $post['validation_error'])
    {
        LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.account.edit.module.error', ['error' => $post['validation_error']], $textDomain));
    }
    else
    {
        reset($post);

        $userPrefsRepository = Doctrine::getRepository('LotgdCore:ModuleUserprefs');

        $messages = '';

        foreach ($post as $key => $val)
        {
            $entity = $userPrefsRepository->findOneBy(['modulename' => $module, 'setting' => $key, 'userid' => $userId]);

            Doctrine::persist($userPrefsRepository->hydrateEntity([
                'modulename' => $module,
                'setting'    => $key,
                'userid'     => $userId,
                'value'      => $val,
            ], $entity));
            $messages .= LotgdTranslator::t('flash.message.account.edit.module.setting', ['key' => $module, 'val' => $val], $textDomain);
        }

        Doctrine::flush();

        $messages .= LotgdTranslator::t('flash.message.account.edit.module.saved', ['module' => $module], $textDomain);

        LotgdFlashMessages::addSuccessMessage($messages);
    }

    $op = 'edit';
    LotgdRequest::setQuery('op', 'edit');
    LotgdRequest::setQuery('subop', 'module');
}

$row = $characterInfo ?? null;

switch ($op)
{
    case 'edit':
        $params['tpl'] = 'edit';

        $subop = (string) LotgdRequest::getQuery('subop');

        if ('' != $petition)
        {
            LotgdNavigation::addHeader('common.category.navigation');
            LotgdNavigation::addNav('user.nav.petition', "viewpetition.php?op=view&id={$petition}");
        }

        LotgdNavigation::addHeader('user.category.operations');
        LotgdNavigation::addNav('user.nav.last.hit', "user.php?op=lasthit&userid={$userId}");
        LotgdNavigation::addNav('user.nav.debuglog', "user.php?op=debuglog&userid={$userId}{$returnpetition}");
        LotgdNavigation::addNav('user.nav.bio', "bio.php?char={$userId}&ret=".urlencode(LotgdRequest::getServer('REQUEST_URI')));

        if (($session['user']['superuser'] & SU_EDIT_DONATIONS) !== 0)
        {
            LotgdNavigation::addNav('user.nav.donation', 'donators.php?op=add1&name='.rawurlencode($row['login']).'&ret='.urlencode(LotgdRequest::getServer('REQUEST_URI')));
        }

        LotgdNavigation::addHeader('user.category.bans');
        LotgdNavigation::addNav('user.nav.ban.setup', "bans.php?op=setupban&userid={$userId}");

        module_editor_navs('prefs', "user.php?op=edit&subop=module&userid={$userId}{$returnpetition}&module=");

        if ('' == $subop)
        {
            $lotgdFormFactory    = LotgdKernel::get('form.factory');
            $type                = (string) LotgdRequest::getQuery('type') ?: 'acct';
            $characterRepository = Doctrine::getRepository('LotgdCore:Avatar');

            $accountEntity   = $repository->find($userId);
            $characterEntity = $characterRepository->find($accountEntity->getAvatar()->getId());

            $class  = 'acct' == $type ? AccountsType::class : CharactersType::class;
            $entity = 'acct' == $type ? $accountEntity : $characterEntity;

            $form = $lotgdFormFactory->create($class, $entity, [
                'action' => "user.php?op=edit&type={$type}&userid={$userId}{$returnpetition}",
                'attr'   => [
                    'autocomplete' => 'off',
                ],
            ]);

            $form->handleRequest(LotgdRequest::_i());

            if ($form->isSubmitted() && $form->isValid())
            {
                $entity = $form->getData();

                Doctrine::persist($entity);
                Doctrine::flush();

                $message = ('acct' == $type) ? 'flash.message.account.edit.saved.account' : 'flash.message.account.edit.saved.character';

                $name = 'acct' == $type ? 'getLogin' : 'getName';

                LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t($message, ['name' => $entity->{$name}()], $textDomain));

                if ($session['user']['acctid'] == $userId)
                {
                    $session['user'] = $repository->getUserById($userId);
                }
            }

            LotgdNavigation::addNavAllow("user.php?op=edit&type={$type}&userid={$userId}{$returnpetition}");

            $params['form']         = $form->createView();
            $params['userLoggedIn'] = $accountEntity->getLoggedin();
            $params['type']         = $type;

            //-- Avoid Doctrine save a invalid Form
            unset($entity, $accountEntity, $characterEntity);
        }
        elseif ('module' == $subop)
        {
            $module = (string) LotgdRequest::getQuery('module');

            LotgdNavigation::addHeader('user.category.operations');
            LotgdNavigation::addNav('user.nav.edit', "user.php?op=edit&userid={$userId}{$returnpetition}");

            $info = get_module_info($module);

            if (\count($info['prefs']) > 0)
            {
                $data      = [];
                $msettings = [];

                foreach ($info['prefs'] as $key => $val)
                {
                    // Handle vals which are arrays.
                    if (\is_array($val))
                    {
                        $v      = $val[0];
                        $x      = explode('|', $v);
                        $val[0] = $x[0];
                        $x[0]   = $val;
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

                $userPrefsRepository = Doctrine::getRepository('LotgdCore:ModuleUserprefs');
                $result              = $userPrefsRepository->findBy(['modulename' => $module, 'userid' => $userId]);

                foreach ($result as $row)
                {
                    $data[$row->getSetting()] = $row->getValue();
                }

                LotgdResponse::pageAddContent("<form action='user.php?op=savemodule&module={$module}&userid={$userId}{$returnpetition}' method='POST'>");
                LotgdNavigation::addNavAllow("user.php?op=savemodule&module={$module}&userid={$userId}{$returnpetition}");
                lotgd_showform($msettings, $data);
                LotgdResponse::pageAddContent('</form>');

                LotgdResponse::pageEnd();
            }
            else
            {
                LotgdFlashMessages::addWarningMessage(LotgdTranslator::t('flash.message.account.edit.no.prefs', ['module' => $module], $textDomain));

                redirect("user.php?op=edit&userid={$userId}{$returnpetition}");
            }
        }

    break;
    case 'debuglog':
        $params['tpl'] = 'debuglog';

        LotgdNavigation::addHeader('user.category.operations');
        LotgdNavigation::addNav('user.nav.edit', "user.php?op=edit&userid={$userId}{$returnpetition}");
        LotgdNavigation::addNav('user.nav.refresh', "user.php?op=debuglog&userid={$userId}{$returnpetition}");

        $debugLogRepository = Doctrine::getRepository('LotgdCore:Debuglog');

        $query = $debugLogRepository->createQueryBuilder('u');

        $query->select('u.id', 'u.date', 'u.actor', 'u.target', 'u.message', 'u.field', 'u.value')
            ->addSelect('c.name AS actorName', 'c2.name AS targetName')
            ->leftJoin('LotgdCore:Avatar', 'c', 'WITH', $query->expr()->eq('c.acct', 'u.actor'))
            ->leftJoin('LotgdCore:Avatar', 'c2', 'WITH', $query->expr()->eq('c2.acct', 'u.target'))
            ->where('u.actor = :acct OR u.target = :acct')

            ->orderBy('u.date', 'DESC')

            ->setParameter('acct', $userId)
        ;

        $params['paginator'] = $debugLogRepository->getPaginator($query, $page, 500);

    break;
    default:
        $params['tpl'] = 'default';

        $repoAcctEveryPage = Doctrine::getRepository('LotgdCore:AccountsEverypage');

        $page  = (int) LotgdRequest::getQuery('page');
        $sort  = (string) LotgdRequest::getQuery('sort');
        $order = $sort ?: 'acctid';

        $query = (string) LotgdRequest::getPost('q');
        $query = (string) ($query ?: LotgdRequest::getQuery('q'));

        $params['query']     = '' !== $query && '0' !== $query ? "q={$query}" : '';
        $params['paginator'] = $repository->userSearchAccounts($query, $order, $page);
        $params['stats']     = $repoAcctEveryPage->getStatsPageGen();

        $params['paginatorLink'] = LotgdRequest::getServer('REQUEST_URI');

    break;
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/user.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();

function show_bitfield($val)
{
    $out = '';
    $v   = 1;

    for ($i = 0; $i < 32; ++$i)
    {
        $out .= ((int) $val & $v) !== 0 ? '1' : '0';
        $v *= 2;
    }

    return $out;
}
