<?php

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
$superusers = $repository->getSuperuserCountWithPermit(SU_MEGAUSER);

if (0 == $superusers)
{
    $name = (string) \LotgdHttp::getPost('name');
    $pass1 = (string) \LotgdHttp::getPost('pass1');
    $pass2 = (string) \LotgdHttp::getPost('pass2');
    if ($name)
    {
        $showform = false;

        if ($pass1 != $pass2)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage10.post.passwordNotMatch', [], 'page-installer'));
            $showform = true;
        }
        elseif (strlen($pass1) < 6)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage10.post.passwordShort', [], 'page-installer'));
            $showform = true;
        }
        else
        {
            // Give the superuser a decent set of privs so they can
            // do everything needed without having to first go into
            // the user editor and give themselves privs.
            $su = SU_MEGAUSER | SU_EDIT_MOUNTS | SU_EDIT_CREATURES | SU_EDIT_PETITIONS | SU_EDIT_COMMENTS | SU_EDIT_DONATIONS |
            SU_EDIT_USERS | SU_EDIT_CONFIG | SU_INFINITE_DAYS | SU_EDIT_EQUIPMENT | SU_EDIT_PAYLOG | SU_DEVELOPER |
            SU_POST_MOTD | SU_MODERATE_CLANS | SU_EDIT_RIDDLES | SU_MANAGE_MODULES | SU_AUDIT_MODERATION | SU_RAW_SQL |
            SU_VIEW_SOURCE | SU_NEVER_EXPIRE;
            $pass = md5(md5(stripslashes(httppost('pass1'))));
            $sql = 'DELETE FROM '.DB::prefix('accounts')." WHERE login='$name'";
            DB::query($sql);

            try
            {
                //-- Configure account
                $account = new \Lotgd\Core\Entity\Accounts();
                $account->setLogin((string) $name)
                    ->setPassword((string) $pass)
                    ->setSuperuser($su)
                    ->setRegdate(new \DateTime())
                ;

                //-- Need for get a ID of new account
                \Doctrine::persist($account);
                \Doctrine::flush(); //Persist objects

                //-- Configure character
                $character = new \Lotgd\Core\Entity\Characters();
                $character->setPlayername((string) $name)
                    ->setName("`%Admin`0 `&{$name}`0")
                    ->setCtitle('`%Admin`0')
                    ->setAcct($account)
                ;

                //-- Need for get ID of new character
                \Doctrine::persist($character);
                \Doctrine::flush(); //-- Persist objects

                //-- Set ID of character and update Account
                $account->setCharacter($character);
                \Doctrine::persist($account);
                \Doctrine::flush(); //-- Persist objects

                \Doctrine::clear();//-- Detaches all objects from Doctrine!
            }
            catch (\Throwable $th)
            {
                die('Failed to create Admin account and character. Your first check should be to make sure that MYSQL (if that is your type) is not in strict mode.');
            }

            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage10.post.success', ['name' => $name], 'page-installer'));
            savesetting('installer_version', \Lotgd\Core\Application::VERSION);

            return redirect('home.php');
        }
    }
    else
    {
        $showform = true;
        savesetting('installer_version', \Lotgd\Core\Application::VERSION);
    }

    if ($showform)
    {
        $params = [
            'stage' => $stage,
            'name' => $name
        ];

        rawoutput(LotgdTheme::renderLotgdTemplate('core/page/installer/stage-10.twig', $params));
    }
}
else
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage10.alreadySuperuser', [], 'page-installer'));

    savesetting('installer_version', \Lotgd\Core\Application::VERSION);

    return redirect('home.php');
}
