<?php

$needsauthentication = false;
if (DB_CHOSEN)
{
    $acctRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
    $countSuperuser = $acctRepository->getSuperuserCountWithPermit(SU_MEGAUSER);

    if (0 == $countSuperuser)
    {
        $needsauthentication = false;
    }
    elseif($countSuperuser > 0)
    {
        $needsauthentication = true;
    }

    $name = \LotgdHttp::getPost('username', '');
    if ($name > '')
    {
        $password = stripslashes((string) \LotgdHttp::getPost('password', ''));
        $result = $acctRepository->getLoginSuperuserWithPermit($name, $password, SU_MEGAUSER);

        if (count($result) > 0)
        {
            $row = $result[0];
            // Okay, we have a username with megauser, now we need to do
            // some hackery with the password.
            $needsauthentication = true;
            $p1 = md5($password);
            $p2 = md5($p1);

            if ('-1' == getsetting('installer_version', '-1'))
            {
                // Okay, they are upgrading from 0.9.7  they will have
                // either a non-encrypted password, or an encrypted singly
                // password.
                if (32 == strlen($row['password']) && $row['password'] == $p1 || $row['password'] == $password)
                {
                    $needsauthentication = false;
                }
            }
            elseif ($row['password'] == $p2)
            {
                $needsauthentication = false;
            }

            if (false === $needsauthentication)
            {
                \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('stage0.authentication.login.fail', [], 'page-installer'));

                return redirect('installer.php?stage=1');
            }
        }
        else
        {
            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('stage0.authentication.login.fail', [], 'page-installer'));

            return redirect('installer.php?stage=0');
        }

        unset($password);
    }
}

//-- If a user with appropriate privs is already logged in, let's let them past.
if ($session['user']['superuser'] & SU_MEGAUSER)
{
    $needsauthentication = false;
}

if ($needsauthentication)
{
    $session['installer']['stagecompleted'] = -1;
}

$params = [
    'authentication' => $needsauthentication
];

rawoutput(LotgdTheme::renderLotgdTemplate('core/pages/installer/stage-0.twig', $params));
