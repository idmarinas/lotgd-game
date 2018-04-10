<?php
// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/is_email.php';
require_once 'lib/checkban.php';
require_once 'lib/http.php';
require_once 'lib/sanitize.php';
require_once 'lib/settings_extended.php';
require_once 'lib/serverfunctions.class.php';

checkban();

tlschema('create');

$trash = getsetting('expiretrashacct', 1);
$new = getsetting('expirenewacct', 10);
$old = getsetting('expireoldacct', 45);

$op = httpget('op');

if ($op == 'val' || $op == 'forgotval')
{
    if (ServerFunctions::isTheServerFull() == true)
    {
        //server is full, your "cheat" does not work here buddy ;) you can't bypass this!
        addnav('Login', 'index.php');

        page_header('Account Validation');

        rawoutput($lotgd_tpl->renderThemeTemplate('pages/create/serverfull.twig', []));

        page_footer();
	}
}

page_header('Create A Character');

if ($op == 'forgotval')
{
	$id = httpget('id');
    $select = DB::select('accounts');
    $select->columns(['acctid', 'login' , 'superuser', 'password', 'name', 'replaceemail', 'emailaddress', 'emailvalidation'])
        ->where->equalTo('forgottenpassword', $id)
            ->notEqualTo('forgottenpassword' , '')
    ;

    $result = DB::execute($select);

    if ($result->count() > 0)
    {
        $row = $result->current();

        $update = DB::update('accounts');
        $update->set(['forgottenpassword' => ''])
            ->where->equalTo('forgottenpassword', $id);
        DB::execute($update);

		//rare case: we have somebody who deleted his first validation email and then requests a forgotten PW...
        if ($row['emailvalidation'] != '' && substr($row['emailvalidation'], 0, 1) != 'x')
        {
            $update = DB::update('accounts');
            $update->set(['emailvalidation' => ''])
                ->where->equalTo('acctid', $row['acctid']);
            DB::execute($update);
        }

        $data = [
            'login' => $row['login'],
            'password' => "!md52!{$row['password']}",
            'trash' => $trash,
            'new' => $new,
            'old' => $old,
        ];

        rawoutput($lotgd_tpl->render('pages/forgotval/result.twig', $data));
    }
    else
    {
        rawoutput($lotgd_tpl->render('pages/forgotval/noresult.twig', []));
    }
}
elseif ($op == 'val')
{
	$id = httpget('id');
    $select = DB::select('accounts');
    $select->columns(['acctid', 'login' , 'superuser', 'password', 'name', 'replaceemail', 'emailaddress'])
        ->where->equalTo('emailvalidation', $id)
            ->notEqualTo('emailvalidation' , '')
    ;

    $result = DB::execute($select);

    if ($result->count() > 0)
    {
		$row = $result->current();
        $dataTpl = [
            'login' => $row['login'],
            'password' => "!md52!{$row['password']}",
            'trash' => $trash,
            'new' => $new,
            'old' => $old,
        ];
        if ($row['replaceemail'] != '')
        {
            require_once 'lib/debuglog.php';

			$replace_array = explode('|', $row['replaceemail']);
			$replaceemail = $replace_array[0]; //1==date
			//note: remove any forgotten password request!

            $update = DB::update('accounts');
            $update->set(['emailaddress' => $replaceemail, 'replaceemail' => '', 'forgottenpassword' => ''])
                ->where->equalTo('emailvalidation', $id);
            DB::execute($update);

			$data['messages'][] = '`#`c Email changed successfully!`c`0`n';
            debuglog("Email change request validated by link from ".$row['emailaddress']." to ".$replaceemail, $row['acctid'], $row['acctid'], 'Email');

			//If a superuser changes email, we want to know about it... at least those who can ee it anyway, the user editors...
            if ($row['superuser'] > 0)
            {
				// 5 failed attempts for superuser, 10 for regular user
				// send a system message to admin
                require_once 'lib/systemmail.php';

				$sql = "SELECT acctid FROM " . DB::prefix("accounts") ." WHERE (superuser&".SU_EDIT_USERS.")";
				$result2 = DB::query($sql);
				$subj = translate_mail(['`#%s`j has changed the email address', $row['name']], 0);
				$alert = translate_mail(["Email change request validated by link to %s from %s originally for login '%s'.", $replaceemail, $row['emailaddress'], $row['login']], 0);
                while ($row2 = DB::fetch_assoc($result2))
                {
					$msg = translate_mail(['This message is generated as a result of an email change to a superuser account. Log Follows:`n`n%s', $alert], 0);
					systemmail($row2['acctid'], $subj, $msg, 0, $noemail);
				}
			}
        }
        $update = DB::update('accounts');
        $update->set(['emailvalidation' => ''])
            ->where->equalTo('emailvalidation', $id);
        DB::execute($update);

		$dataTpl['replaceemail'] = $row['replaceemail'];

        savesetting('newestplayer', $row['acctid']);

        rawoutput($lotgd_tpl->render('pages/val/result.twig', $dataTpl));
    }
    else
    {
		rawoutput($lotgd_tpl->render('pages/val/noresult.twig', []));
	}
}

if ($op == 'forgot')
{
    $charname = httppost('charname');

    $data = [
        'requireemail' => getsetting('requireemail', 0)
    ];

	if ($charname != '')
    {
        $select = DB::select('accounts');
        $select->columns(['acctid', 'login', 'emailaddress', 'forgottenpassword', 'password'])
            ->where->equalTo('login', $charname);

        $result = DB::execute($select);

		if ($result->count() > 0)
        {
			$row = DB::fetch_assoc($result);
            if (trim($row['emailaddress']) != '')
            {
                if ($row['forgottenpassword'] == '')
                {
					$row['forgottenpassword'] = substr('x'.md5(date('Y-m-d H:i:s').$row['password']), 0, 32);
					$sql = "UPDATE " . DB::prefix("accounts") . " SET forgottenpassword='{$row['forgottenpassword']}' where login='{$row['login']}'";
					DB::query($sql);
				}

                $subj = translate_mail($settings_extended->getSetting('forgottenpasswordmailsubject'), $row['acctid']);
				$msg = translate_mail($settings_extended->getSetting('forgottenpasswordmailtext'), $row['acctid']);
				$replace = [
					'{login}' => $row['login'],
					'{acctid}' => $row['acctid'],
					'{emailaddress}' => $row['emailaddress'],
					'{requester_ip}' => $_SERVER['REMOTE_ADDR'],
					'{gameurl}' => ($_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http').'://'.($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']),
					'{forgottenid}' => $row['forgottenpassword'],
                ];

				$keys = array_keys($replace);
				$values = array_values($replace);
				$msg = str_replace($keys,$values,$msg);

                lotgd_mail($row['emailaddress'], $subj, str_replace('`n', '\n', $msg));

				$data['messages'][] = '`#Sent a new validation email to the address on file for that account.`0`n';
				$data['messages'][] = '`#You may use the validation email to log in and change your password.`0`n';
            }
            else
            {
				$data['messages'][] = "`#We're sorry, but that account does not have an email address associated with it, and so we cannot help you with your forgotten password.`0`n";
				$data['messages'][] = '`#Use the Petition for Help link at the bottom of the page to request help with resolving your problem.`0`n';
			}
        }
        else
        {
			$data['messages'][] = '`#Could not locate a character with that name.`0`n';
			$data['messages'][] = "`#Look at the List Warriors page off the login page to make sure that the character hasn't expired and been deleted.`0`n";
		}
    }

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/create/forgot.twig', $data));
}

if (0 == getsetting('allowcreation', 1)) { rawoutput($lotgd_tpl->renderThemeTemplate('pages/create/notallowcreation.twig', [])); }
else
{
    $data = [];
    if ($op == 'create')
    {
		$emailverification = '';
		$shortname = sanitize_name(getsetting('spaceinname', 0), httppost('name'));

        if (soap($shortname) != $shortname)
        {
            $data['messages'] = [
                '`$Error: `^Bad language was found in your name, please consider revising it.`n`0'
            ];
			$op = '';
        }
        else
        {
            $blockaccount = false;
			$email = httppost('email');
			$pass1 = httppost('pass1');
			$pass2 = httppost('pass2');
            if (getsetting('blockdupeemail', 0) == 1 && getsetting('requireemail', 0) == 1)
            {
                $select = DB::select('accounts');
                $select->columns(['login'])
                    ->where->equalTo('emailaddress', (string) $email);

                $result = DB::execute($select);

                if ($result->count() > 0)
                {
					$blockaccount = true;
					$data['messages'][] = 'You may have only one account.`n';
				}
			}

			$passlen = (int) httppost('passlen');
            if (substr($pass1, 0, 5) != '!md5!' && substr($pass1, 0, 6) != '!md52!')
            {
				$passlen = strlen($pass1);
            }

            if ($passlen <= 3)
            {
				$data['messages'][] = 'Your password must be at least 4 characters long.`n';
				$blockaccount = true;
            }

            if ($pass1 != $pass2)
            {
				$data['messages'][] = 'Your passwords do not match.`n';
				$blockaccount = true;
            }

            if (strlen($shortname) < 3)
            {
				$data['messages'][] = 'Your name must be at least 3 characters long.`n';
				$blockaccount = true;
            }

            if (strlen($shortname) > 25)
            {
				$data['messages'][] = "Your character's name cannot exceed 25 characters.`n";
				$blockaccount = true;
            }

			if (getsetting('requireemail', 0) == 1 && ! is_email($email) || getsetting('requireemail', 0))
            {
				$data['messages'][] = 'You must enter a valid email address.`n';
				$blockaccount = true;
            }

			$args = modulehook('check-create', httpallpost());
            if(isset($args['blockaccount']) && $args['blockaccount'])
            {
                if (is_array($args['msg']))
                {
                    $data['messages'] = array_merge($data['messages'], $args['msg']);
                }
                else
                {
                    $data['messages'][] = $args['msg'];
                }

				$blockaccount = true;
			}

            if (! $blockaccount)
            {
				$shortname = preg_replace("/\s+/", ' ', $shortname);

                $select = DB::select('accounts');
                $select->columns(['name'])
                    ->where->equalTo('login', $shortname);

                $result = DB::execute($select);

                if ($result->count() > 0)
                {
					$data['messages'][] = '`$Error`^: Someone is already known by that name in this realm, please try again.`0';
					$op = '';
                }
                else
                {
                    require_once 'lib/titles.php';

					$sex = (int) httppost('sex');
					// Inserted the following line to prevent hacking
					// Reported by Eliwood
					if ($sex <> SEX_MALE) $sex = SEX_FEMALE;

					$title = get_dk_title(0, $sex);
                    if (getsetting('requirevalidemail', 0))
                    {
						$emailverification = md5(date('Y-m-d H:i:s').$email);
                    }

                    $refer = httpget('r');
                    $referer = 0;
                    if ($refer > '')
                    {
						$sql = "SELECT acctid FROM " . DB::prefix("accounts") . " WHERE login='".DB::quoteValue($refer)."'";
						$result = DB::query($sql);
						$ref = DB::fetch_assoc($result);
						$referer = $ref['acctid'];
                    }

					$dbpass = '';
                    if (substr($pass1, 0, 5) == "!md5!")
                    {
						$dbpass = md5(substr($pass1, 5));
                    }
                    else
                    {
						$dbpass = md5(md5($pass1));
                    }

					$sql = "INSERT INTO " . DB::prefix("accounts") . "
						(playername,name, superuser, title, password, sex, login, laston, uniqueid, lastip, gold, location, emailaddress, emailvalidation, referer, regdate)
						VALUES
                        ('$shortname','$title $shortname', '".getsetting("defaultsuperuser",0)."', '$title', '$dbpass', '$sex', '$shortname', '".date("Y-m-d H:i:s",strtotime("-1 day"))."', '".$_COOKIE['lgi']."', '".$_SERVER['REMOTE_ADDR']."', ".getsetting("newplayerstartgold",50).", '".addslashes(getsetting('villagename', LOCATION_FIELDS))."', '$email', '$emailverification', '$referer', NOW())";

                    DB::query($sql);

                    if (DB::affected_rows() <= 0)
                    {
						$data['messages'][] = '`$Error`^: Your account was not created for an unknown reason, please try again. ';
                    }
                    else
                    {
						$sql = "SELECT acctid, emailaddress  FROM " . DB::prefix('accounts') . " WHERE login='$shortname'";
						$result = DB::query($sql);
						$row = DB::fetch_assoc($result);
						$args = httpallpost();
						$args['acctid'] = $row['acctid'];
						//insert output
						$sql_output = "INSERT INTO " . DB::prefix('accounts_output') . " VALUES ({$row['acctid']}, '');";
						DB::query($sql_output);
						//end
                        modulehook('process-create', $args);

                        if ($emailverification != '')
                        {
							$subj = translate_mail($settings_extended->getSetting('verificationmailsubject'), 0);
							$msg = translate_mail($settings_extended->getSetting('verificationmailtext'), 0);
							$replace = [
								'{login}' => $shortname,
								'{acctid}' => $row['acctid'],
								'{emailaddress}' => $row['emailaddress'],
								'{gameurl}' => ($_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http').'://'.($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']),
								'{validationid}' => $emailverification,
							];

							$keys = array_keys($replace);
							$values = array_values($replace);
                            $msg = str_replace($keys,$values,$msg);

                            lotgd_mail($email, $subj, str_replace('`n','\n', $msg));
                        }
                        else
                        {
                            savesetting('newestplayer', $row['acctid']);
                        }

                        $data = [
                            'emailverification' => $emailverification,
                            'email' => $email,
                            'trash' => $trash,
                            'new' => $new,
                            'old' => $old,
                            'shortname' => $shortname
                        ];

                        rawoutput($lotgd_tpl->renderThemeTemplate('pages/create/created.twig', $data));
					}
				}
            }
            else
            {
                array_unshift($data['messages'], '`$Error:`0`n');
				$op = '';
			}
		}
    }

    if ($op == '')
    {
        $refer = httpget('r');
        if ($refer) $refer = '&r=' . htmlentities($refer, ENT_COMPAT, getsetting('charset', 'UTF-8'));

		$reqbool = true;
        $req = '`^(optional -- however, if you choose not to enter one, there will be no way that you can reset your password if you forget it!)`0';

        if (getsetting('requireemail', 0) == 0)
        {
			$req = '`$(required, an email will be sent to this address to verify it before you can log in)`0';
			$reqbool = false;
        }
        elseif (getsetting('requirevalidemail', 0) == 0) { $req = '`$(required)`0'; }

        $data = array_merge($data, [
                'formurlsubmit' => "create.php?op=create$refer",
                'trash' => $trash,
                'new' => $new,
                'old' => $old,
                'reqemail' => $reqbool,
                'reqemailtext' => $req
            ]
        );

        $data = modulehook('create-form', $data);

		rawoutput($lotgd_tpl->renderThemeTemplate('pages/create/register.twig', $data));
	}
}
addnav('Login page');
addnav('Login', 'index.php');

page_footer();
