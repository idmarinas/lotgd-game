<?php

/**
 * This file is part of "LoTGD Core Package".
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Controller;

use DateTime;
use Lotgd\Core\Entity\Petitions;
use Lotgd\Core\Event\Core;
use Lotgd\Core\Form\PetitionType;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Pattern\LotgdControllerTrait;
use Lotgd\Core\Repository\PetitionsRepository;
use Lotgd\Core\Tool\LotgdMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tracy\Debugger;

class PetitionController extends AbstractController
{
    use LotgdControllerTrait;

    public const TRANSLATION_DOMAIN = 'jaxon_petition';

    private $dispatcher;
    private $petitionsRepository;
    private $translator;
    private $settings;
    private LotgdMail $mailer;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        PetitionsRepository $petitionsRepository,
        TranslatorInterface $translator,
        Settings $settings,
        LotgdMail $mailer
    ) {
        $this->dispatcher          = $dispatcher;
        $this->petitionsRepository = $petitionsRepository;
        $this->translator          = $translator;
        $this->settings            = $settings;
        $this->mailer              = $mailer;
    }

    public function help(HttpFoundationRequest $request): Response
    {
        global $session;

        $params    = $this->getParams();
        $form      = $this->createForm(PetitionType::class);
        $formEmpty = clone $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $post  = $form->getData();
            $count = $this->petitionsRepository->getCountPetitionsForNetwork($request->server->get('REMOTE_ADDR'), $request->cookies->get('lgi'));

            if ($count >= 5 && ! (isset($session['user']['superuser']) && $session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO))
            {
                $this->addNotification('error', [
                    'notification' => $this->translator->trans('flash.message.section.default.error.network', ['count' => $count], self::TRANSLATION_DOMAIN),
                    'duration'     => 30000,
                ]);

                return $this->renderBlock('petition/help.html.twig', 'content', array_merge($params, [
                    'is_post' => $form->isSubmitted(),
                    'form'    => $form->createView(),
                ]));
            }

            $this->proccessForm($request, $post, $form, $formEmpty);

            $isSubmitted = true;
        }
        // -- Default data when user have session active and not is post
        elseif ( ! $form->isSubmitted() && $session['user']['loggedin'] ?? false)
        {
            $form->setData([
                'charname' => $session['user']['name'],
                'email'    => $session['user']['emailaddress'],
            ]);
        }

        $isSubmitted ??= $form->isSubmitted();

        return $this->renderBlock('petition/help.html.twig', $isSubmitted ? 'content' : 'dialog', array_merge($params, [
            'is_post' => $isSubmitted,
            'form'    => $form->createView(),
        ]));
    }

    public function faq(Request $request): Response
    {
        $params = $this->getParams();
        $faq    = $request->query->getInt('faq', 0);
        $faq    = max(0, min(3, $faq));

        if (0 == $faq)
        {
            $params['faq_list'] = $this->faqToc();

            return $this->renderBlock('petition/faq.html.twig', 'petition_faq', $params);
        }

        return $this->renderBlock('petition/faq.html.twig', "petition_faq{$faq}", $params);
    }

    public function primer(): Response
    {
        $params = $this->getParams();

        return $this->renderBlock('petition/faq.html.twig', 'petition_primer', $params);
    }

    public function report(Request $request): Response
    {
        global $session;

        // -- Not proccess if not are loggedin
        if ( ! ($session['user']['loggedin'] ?? false))
        {
            return null;
        }

        $playerId = $request->query->getInt('player_id', 0);
        $message  = $request->query->get('message', '');

        $params = $this->getParams();

        $form = $this->createForm(PetitionType::class);
        $form->remove('problem_type');
        $form->add('playerAbuseId', HiddenType::class);
        $form->add('abuseMessage', HiddenType::class);
        $formEmpty = clone $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();

            $this->proccessForm($request, $post, $form, $formEmpty);

            $isSubmitted = true;
        }
        // -- Default data when user have session active and not is post
        elseif ( ! $form->isSubmitted() && $session['user']['loggedin'] ?? false)
        {
            $form->setData([
                'charname'      => $session['user']['name'],
                'email'         => $session['user']['emailaddress'],
                'playerAbuseId' => $playerId,
                'abuseMessage'  => $message,
            ]);
        }

        $isSubmitted ??= $form->isSubmitted();

        return $this->renderBlock('petition/report.html.twig', $isSubmitted ? 'content' : 'dialog', array_merge($params, [
            'is_post' => $isSubmitted,
            'form'    => $form->createView(),
        ]));
    }

    /**
     * If the admin wants it, email the petitions to them.
     */
    private function emailPetitionAdmin(Request $request, string $name, array $post): void
    {
        if ('' === $this->settings->getSetting('emailpetitions', 0) || '0' === $this->settings->getSetting('emailpetitions', 0))
        {
            return;
        }

        $date = date('Y-m-d H:i:s');
        $url  = $this->settings->getSetting('serverurl', $request->server->get('SERVER_NAME'));

        if ( ! preg_match('/\\/$/', $url))
        {
            $url .= '/';
            $this->settings->saveSetting('serverurl', $url);
        }

        $tlServer  = $this->translator->trans('section.default.petition.mail.server', [], self::TRANSLATION_DOMAIN);
        $tlAuthor  = $this->translator->trans('section.default.petition.mail.author', [], self::TRANSLATION_DOMAIN);
        $tlDate    = $this->translator->trans('section.default.petition.mail.date', [], self::TRANSLATION_DOMAIN);
        $tlBody    = $this->translator->trans('section.default.petition.mail.body', [], self::TRANSLATION_DOMAIN);
        $tlSubject = $this->translator->trans('section.default.petition.mail.subject', ['url' => $request->server->get('SERVER_NAME')], self::TRANSLATION_DOMAIN);

        $msg = "{$tlServer}: {$url}\n";
        $msg .= "{$tlAuthor}: {$name}\n";
        $msg .= "{$tlDate} : {$date}\n";
        $msg .= "{$tlBody} :\n".Debugger::dump($post, 'Post')."\n";

        $this->mailer->sendEmail(new Address($this->settings->getSetting('gameadminemail', 'postmaster@localhost.com')), $tlSubject, $msg);
    }

    private function proccessForm(Request $request, $post, &$form, $formEmpty): void
    {
        global $session;

        $session['user']['acctid']   ??= 0;
        $session['user']['password'] ??= '';

        $p = $session['user']['password'];
        unset($session['user']['password']);

        $post['cancelpetition'] ??= false;
        $post['cancelreason'] = $post['cancelreason'] ?? '' ?: $this->translator->trans('section.default.post.cancel', [], self::TRANSLATION_DOMAIN);

        $post = new Core($post);
        $this->dispatcher->dispatch($post, Core::PETITION_ADD);
        $post = $post->getData();

        if ($post['cancelpetition'])
        {
            $this->addNotification('warning', [
                'notification' => $this->translator->trans('flash.message.section.default.error.cancel', ['reason' => $post['cancelreason']], self::TRANSLATION_DOMAIN),
                'duration'     => 30000,
            ]);

            return;
        }

        $entity = (new Petitions())
            ->setAuthor($session['user']['acctid'])
            ->setDate(new DateTime('now'))
            ->setBody($post)
            ->setPageinfo($session)
            ->setIp($request->server->get('REMOTE_ADDR'))
            ->setId($request->cookies->get('lgi'))
        ;

        $session['user']['password'] = $p;

        $this->getDoctrine()->getManager()->persist($entity);
        $this->getDoctrine()->getManager()->flush();

        // If the admin wants it, email the petitions to them.
        $this->emailPetitionAdmin($request, $post['charname'] ?: '', $post);

        $form = $formEmpty;

        $this->addNotification('success', $this->translator->trans('flash.message.section.default.success.send', [], self::TRANSLATION_DOMAIN));
    }

    /**
     * Get default params.
     */
    private function getParams(): array
    {
        return [
            'text_domain'  => self::TRANSLATION_DOMAIN,
            'days_per_day' => $this->settings->getSetting('daysperday', 2),
            'multimaster'  => (int) $this->settings->getSetting('multimaster', 1),
        ];
    }

    /**
     * Creaqte list of faqs.
     */
    private function faqToc(): array
    {
        $args = new Core([
            [
                'attr' => [
                    'data-action' => 'click->petition#primer',
                ],
                'link' => [
                    'section.faq.toc.primer',
                    [],
                    self::TRANSLATION_DOMAIN,
                ],
            ],
            [
                'attr' => [
                    'data-action' => 'click->petition#faq1',
                ],
                'link' => [
                    'section.faq.toc.general',
                    [],
                    self::TRANSLATION_DOMAIN,
                ],
            ],
            [
                'attr' => [
                    'data-action' => 'click->petition#faq2',
                ],
                'link' => [
                    'section.faq.toc.spoiler',
                    [],
                    self::TRANSLATION_DOMAIN,
                ],
            ],
            [
                'attr' => [
                    'data-action' => 'click->petition#faq3',
                ],
                'link' => [
                    'section.faq.toc.technical',
                    [],
                    self::TRANSLATION_DOMAIN,
                ],
            ],
        ]);

        $this->dispatcher->dispatch($args, Core::PETITION_FAQ_TOC);

        return $args->getData();
    }
}
