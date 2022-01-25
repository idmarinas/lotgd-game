<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Controller\Pattern\RenderBlockTrait;
use DateTime;
use Lotgd\Core\Entity\Avatar;
use Throwable;
use Tracy\Debugger;
use Lotgd\Core\Entity\User;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Output\Censor;
use Lotgd\Core\Output\Format;
use Lotgd\Core\Tool\Sanitize;
use Lotgd\Core\Tool\Tool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateController extends AbstractController
{
    use RenderBlockTrait;

    private $dispatcher;
    private $translator;
    private $censor;
    private $sanitize;
    private $format;
    private $log;
    private $settings;
    private $passwordEncoder;
    private $tool;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
        Censor $censor,
        Sanitize $sanitize,
        Format $format,
        Log $log,
        Settings $settings,
        UserPasswordEncoderInterface $passwordEncoder,
        Tool $tool
    ) {
        $this->dispatcher      = $dispatcher;
        $this->translator      = $translator;
        $this->censor          = $censor;
        $this->sanitize        = $sanitize;
        $this->format          = $format;
        $this->log             = $log;
        $this->settings        = $settings;
        $this->passwordEncoder = $passwordEncoder;
        $this->tool            = $tool;
    }

    public function forgotVal(array $params, Request $request): Response
    {
        /** @var Lotgd\Core\Repository\UserRepository $accountRepo */
        $accountRepo   = $this->getDoctrine()->getRepository('LotgdCore:User');
        $forgottenCode = $request->query->getInt('id');

        $account = $accountRepo->findOneBy(['forgottenpassword' => $forgottenCode]);

        if ( ! $account)
        {
            $this->addFlash('warning', $this->translator->trans('validating.pass.paragraph', [], $params['textDomain']));

            return $this->redirect('home.php');
        }

        //-- Delete code of fogotten password
        $account->setForgottenpassword('');

        //-- Save
        $this->getDoctrine()->getManager()->persist($account);

        //-- Rare case: we have somebody who deleted his first validation email and then requests a forgotten PW...
        if ('' != $account->getEmailvalidation() && 'x' != substr($account->getEmailvalidation(), 0, 1))
        {
            $account->getEmailvalidation('');
        }

        $this->getDoctrine()->getManager()->flush(); //-- Persist objects

        $params['account'] = $account;

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_CREATE_FORGOTVAL);
        $params = modulehook('page-create-forgotval-tpl-params', $args->getArguments());

        return $this->renderBlock('page/_blocks/_create.html.twig', 'create_forgot_val', $params);
    }

    public function val(array $params, Request $request): Response
    {
        /** @var Lotgd\Core\Repository\UserRepository $accountRepo */
        $accountRepo = $this->getDoctrine()->getRepository('LotgdCore:User');
        $code        = $request->query->getInt('id');

        $account = $accountRepo->findOneBy(['emailvalidation' => $code]);

        if ( ! $account)
        {
            $this->addFlash('warning', $this->translator->trans('validating.email.paragraph.fail', [], $params['textDomain']));

            return $this->redirect('home.php');
        }

        $params['showLoginButton'] = ! ($account->getReplaceemail());

        if ($account->getReplaceemail())
        {
            $params['emailChanged'] = true;
            $replace_array          = explode('|', $account->getReplaceemail());
            $replaceemail           = $replace_array[0]; //1==date
            $oldEmail               = $account->getEmailaddress();

            $this->log->debug('Email change request validated by link from '.$oldEmail.' to '.$replaceemail, $account->getAcctid(), $account->getAcctid(), 'Email');

            //-- Note: remove any forgotten password request!
            $account->setReplaceemail('')
                ->setEmailaddress($replaceemail)
                ->setForgottenpassword('')
            ;

            //-- If a superuser changes email, we want to know about it... at least those who can ee it anyway, the user editors...
            if ($account->getSuperuser() > 0)
            {
                // 5 failed attempts for superuser, 10 for regular user
                // send a system message to admin
                $acctSuper = $accountRepo->getSuperuserWithPermit(SU_EDIT_USERS);

                if ( ! empty($acctSuper))
                {
                    $subj  = $this->translator->trans('yeoldemail.subject', ['name' => $account->getName()], $params['textDomain']);
                    $alert = $this->translator->trans('yeoldemail.alert', [
                        'newEmail' => $replaceemail,
                        'oldMail'  => $oldEmail,
                        'login'    => $account->getLogin(),
                    ], $params['textDomain']);
                    $msg = $this->translator->trans('yeoldemail.message', ['alert' => $alert], $params['textDomain']);

                    foreach ($acctSuper as $user)
                    {
                        systemmail($user['acctid'], $subj, $msg, 0);
                    }
                }
            }
        }

        //-- Delete code of email validation
        $account->setEmailvalidation('');

        //-- Save
        $this->getDoctrine()->getManager()->persist($account);
        $this->getDoctrine()->getManager()->flush(); //-- Persist objects

        $params['account'] = $account;

        $this->settings->saveSetting('newestplayer', $account->getAcctid());
        $this->settings->saveSetting('newestplayername', $account->getAvatar()->getName());

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_CREATE_VAL);
        $params = modulehook('page-create-val-tpl-params', $args->getArguments());

        return $this->renderBlock('page/_blocks/_create.html.twig', 'create_email_val', $params);
    }

    public function forgot(array $params, Request $request): Response
    {
        $accountRepo = $this->getDoctrine()->getRepository('LotgdCore:User');
        $charname    = (string) $request->request->get('charname', '');

        if ('' !== $charname && '0' !== $charname)
        {
            $account = $accountRepo->findOneBy(['login' => $charname]);

            if ( ! $account instanceof User)
            {
                $this->addFlash('warning', $this->translator->trans('forgot.account.notFound', [], $params['textDomain']));

                return $this->redirect('create.php?op=forgot');
            }

            if ('' === trim($account->getEmailaddress()) || '0' === trim($account->getEmailaddress()))
            {
                $this->addFlash('warning', $this->translator->trans('forgot.account.noEmail', [], $params['textDomain']));

                return $this->redirect('create.php?op=forgot');
            }

            if ('' == $account->getForgottenpassword())
            {
                $account->setForgottenpassword(substr('x'.md5(date('Y-m-d H:i:s').$account->getPassword()), 0, 32));
            }

            $language = ($account->getPrefs()['language'] ?? '') ?: $this->settings->getSetting('defaultlanguage', 'en');

            $subj = $this->translator->trans('forgotpassword.subject', [], 'app_mail', $language);
            $msg  = $this->translator->trans('forgotpassword.body', [
                'login'        => $account->getLogin(),
                'acctid'       => $account->getAcctid(),
                'emailaddress' => $account->getEmailaddress(),
                'requester_ip' => $request->getServer('REMOTE_ADDR'),
                'gameurl'      => '//'.($request->getServer('SERVER_NAME').'/'.$request->getServer('SCRIPT_NAME')),
                'forgottenid'  => $account->getForgottenpassword(),
            ], 'app_mail', $language);

            lotgd_mail($account->getEmailaddress(), $subj, $this->format->colorize($msg));

            $this->addFlash('warning', $this->translator->trans('forgot.account.sent', [], $params['textDomain']));

            return $this->redirect('create.php?op=forgot');
        }

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_CREATE_FORGOT);
        $params = modulehook('page-create-forgot-tpl-params', $args->getArguments());

        return $this->renderBlock('page/_blocks/_create.html.twig', 'create_forgot', $params);
    }

    public function index(array $params, Request $request): Response
    {
        if ('create' == $request->query->get('op'))
        {
            $accountRepo = $this->getDoctrine()->getRepository('LotgdCore:User');

            $emailverification = '';
            $shortname         = trim((string) $request->request->get('name'));
            $shortname         = $this->sanitize->nameSanitize($this->settings->getSetting('spaceinname', 0), $shortname);
            $blockaccount      = false;

            if ($this->censor->filter($shortname) !== $shortname)
            {
                $blockaccount = true;
                $this->addFlash('error', $this->translator->trans('create.account.badLanguage', [], $params['textDomain']));
            }

            $email = (string) $request->request->get('email');
            $pass1 = (string) $request->request->get('pass1');
            $pass2 = (string) $request->request->get('pass2');

            if (1 == $this->settings->getSetting('blockdupeemail', 0) && 1 == $this->settings->getSetting('requireemail', 0))
            {
                $result = $accountRepo->findBy(['emailaddress' => $email]);

                if ( ! empty($result))
                {
                    $this->addFlash('error', $this->translator->trans('create.account.email.duplicate', [], $params['textDomain']));
                    $blockaccount = true;
                }
            }

            if (\strlen($pass1) <= 3)
            {
                $this->addFlash('error', $this->translator->trans('create.account.password.length', [], $params['textDomain']));
                $blockaccount = true;
            }

            if ($pass1 !== $pass2)
            {
                $this->addFlash('error', $this->translator->trans('create.account.password.notIdentical', [], $params['textDomain']));
                $blockaccount = true;
            }

            if (\strlen($shortname) < 3)
            {
                $this->addFlash('error', $this->translator->trans('create.account.name.minLength', [], $params['textDomain']));
                $blockaccount = true;
            }

            if (\strlen($shortname) > 25)
            {
                $this->addFlash('error', $this->translator->trans('create.account.name.maxLength', [], $params['textDomain']));
                $blockaccount = true;
            }

            if (1 == (int) $this->settings->getSetting('requireemail', 0) && ! is_email($email))
            {
                $this->addFlash('error', $this->translator->trans('create.account.email.incorrect', [], $params['textDomain']));
                $blockaccount = true;
            }

            $args = new GenericEvent(null, $request->request->all());
            $this->dispatcher->dispatch($args, Events::PAGE_CREATE_CHECK_CREATION);
            $args = modulehook('check-create', $args->getArguments());

            if (isset($args['blockaccount']) && $args['blockaccount'])
            {
                $blockaccount = true;
            }

            $shortname = preg_replace("/\s+/", ' ', $shortname);
            $result    = $accountRepo->findOneBy(['login' => $shortname]);

            if (null !== $result)
            {
                $blockaccount = true;

                $this->addFlash('error', $this->translator->trans('create.account.name.duplicate', [], $params['textDomain']));
            }

            if ( ! $blockaccount)
            {
                $sex = $request->request->getInt('sex');
                // Inserted the following line to prevent hacking
                // Reported by Eliwood
                if (SEX_MALE != $sex)
                {
                    $sex = SEX_FEMALE;
                }

                $title = $this->tool->getDkTitle(0, $sex);

                if ('' !== $this->settings->getSetting('requirevalidemail', 0) && '0' !== $this->settings->getSetting('requirevalidemail', 0))
                {
                    $emailverification = md5(date('Y-m-d H:i:s').$email);
                }

                $refer   = $request->query->get('r');
                $referer = 0;

                if ($refer > '')
                {
                    $result  = $accountRepo->findOneBy(['login' => $refer]);
                    $referer = $result->getAcctid();
                }

                try
                {
                    //-- Configure account
                    $accountEntity = new User();
                    $accountEntity->setLogin((string) $shortname)
                        ->setSuperuser((int) $this->settings->getSetting('defaultsuperuser', 0))
                        ->setRegdate(new DateTime())
                        ->setUniqueid($request->getCookie('lgi') ?: '')
                        ->setLastip($request->getServer('REMOTE_ADDR'))
                        ->setEmailaddress($email)
                        ->setEmailvalidation($emailverification)
                        ->setReferer($referer)
                    ;

                    $dbpass = $this->passwordEncoder->encodePassword($accountEntity, $pass1);
                    $accountEntity->setPassword($dbpass);

                    //-- Need for get a ID of new account
                    $this->getDoctrine()->getManager()->persist($accountEntity);
                    $this->getDoctrine()->getManager()->flush(); //Persist objects

                    //-- Configure character
                    $characterEntity = new Avatar();
                    $characterEntity->setPlayername((string) $shortname)
                        ->setSex($sex)
                        ->setName("{$title} {$shortname}")
                        ->setTitle($title)
                        ->setGold((int) $this->settings->getSetting('newplayerstartgold', 50))
                        ->setLocation($this->settings->getSetting('villagename', LOCATION_FIELDS))
                        ->setAcct($accountEntity)
                    ;

                    //-- Need for get ID of new character
                    $this->getDoctrine()->getManager()->persist($characterEntity);
                    $this->getDoctrine()->getManager()->flush(); //-- Persist objects

                    //-- Set ID of character and update Account
                    $accountEntity->setAvatar($characterEntity);
                    $this->getDoctrine()->getManager()->persist($accountEntity);
                    $this->getDoctrine()->getManager()->flush(); //-- Persist objects

                    $args           = $request->request->all();
                    $args['acctid'] = $accountEntity->getAcctid();
                    $args           = new GenericEvent(null, $args);
                    $this->dispatcher->dispatch($args, Events::PAGE_CREATE_PROCESS_CREATION);
                    modulehook('process-create', $args->getArguments());

                    if ('' != $emailverification)
                    {
                        $subj = $this->translator->trans('verificationmail.subject', [], 'app_mail');
                        $msg  = $this->translator->trans('verificationmail.body', [
                            'login'        => $shortname,
                            'acctid'       => $accountEntity->getAcctid(),
                            'emailaddress' => $accountEntity->getEmailaddress(),
                            'gameurl'      => 'https://'.($request->getServer('SERVER_NAME').'/'.$request->getServer('SCRIPT_NAME')),
                            'validationid' => $emailverification,
                        ], 'app_mail');

                        lotgd_mail($email, $subj, $this->format->colorize($msg));

                        $this->addFlash('warning', $this->translator->trans('create.account.emailVerification', ['email' => $email], $params['textDomain']));

                        return $this->redirect('index.php');
                    }

                    $this->settings->saveSetting('newestplayer', $accountEntity->getAcctid());
                    $this->settings->saveSetting('newestplayername', $characterEntity->getName());

                    $params['login']    = $shortname;
                    $params['password'] = $pass1;

                    return $this->renderBlock('page/_blocks/_create.html.twig', 'create_account_login', $params);
                }
                catch (Throwable $th)
                {
                    Debugger::log($th);

                    $this->addFlash('error', $this->translator->trans('create.account.error', [], $params['textDomain']));
                }
            }
        }

        /**
         * Get all templates with params for form
         * Example: [
         *      // (string) tplName => array []
         *      'tpl-tame' => ['key' => 'value']
         * ].
         */
        $args = new GenericEvent(null, ['templates' => []]);
        $this->dispatcher->dispatch($args, Events::PAGE_CREATE_FORM);
        $result = modulehook('create-form', $args->getArguments());

        $params['templates'] = $result['templates'];

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_CREATE_POST);
        $params = modulehook('page-create-tpl-params', $args->getArguments());

        return $this->render('page/create.html.twig', $params);
    }
}
