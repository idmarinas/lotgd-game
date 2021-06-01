<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Http;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\View\Helper\HeadTitle;
use Lotgd\Core\Event\EveryRequest;
use Lotgd\Core\Kernel;
use Lotgd\Core\Template\Params;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class Response extends HttpResponse
{
    protected $translator;
    protected $doctrine;
    protected $headTitle;
    protected $template;
    protected $request;
    protected $params;
    /** @var EventDispatcher */
    private $eventDispatcher;
    private $kernel;

    public function __construct(
        EntityManagerInterface $doctrine,
        HeadTitle $headTitle,
        Environment $template,
        Request $request,
        Params $params,
        KernelInterface $kernel
    ) {
        $this->translator      = $kernel->getContainer()->get('translator');
        $this->eventDispatcher = $kernel->getContainer()->get('event_dispatcher');
        $this->doctrine        = $doctrine;
        $this->headTitle       = $headTitle;
        $this->template        = $template;
        $this->request         = $request;
        $this->params          = $params;
        $this->kernel          = $kernel;

        parent::__construct();
    }

    /**
     * Set title for page.
     */
    public function pageTitle(string $message, ?array $parameters = [], string $textDomain = Kernel::TEXT_DOMAIN_DEFAULT, ?string $locale = null): void
    {
        $title = $this->translator->trans($message, $parameters, $textDomain, $locale);

        $this->headTitle->__invoke($title, 'SET');
    }

    /**
     * Start page.
     */
    public function pageStart(?string $title = null, ?array $parameters = [], string $textDomain = Kernel::TEXT_DOMAIN_DEFAULT, ?string $locale = null): void
    {
        global $session;

        if ($title)
        { //-- If not have title not overwrite page title
            $this->pageTitle($title, $parameters, $textDomain, $locale);
        }

        $script = $this->request->getServer('SCRIPT_NAME');
        $script = \substr($script, 0, \strpos($script, '.'));
        $module = (string) $this->request->getQuery('module');

        $args = new EveryRequest(['script' => $script, 'module' => $module]);
        $this->eventDispatcher->dispatch($args, EveryRequest::HEADER);
        $args->setData(modulehook('everyheader', $args->getData()));

        if ($session['user']['loggedin'] ?? false)
        {
            $this->eventDispatcher->dispatch($args, EveryRequest::HEADER_AUTHENTICATED);
            modulehook('everyheader-loggedin', $args->getData());
        }

        calculate_buff_fields();

        $userPre   = $session['user'] ?? [];
        $sesionPre = $session         ?? [];
        unset($sesionPre['user'], $userPre['password']);

        $this->template->addGlobal('userPre', $userPre);
        $this->template->addGlobal('sessionPre', $sesionPre);
    }

    /**
     * Add content to response.
     * To overwrite content use pageSetContent($value) or setContent($value).
     */
    public function pageAddContent(?string $value): void
    {
        if ( ! $value)
        {
            return;
        }

        $this->setContent($this->getContent().$value);
    }

    /**
     * Alias of setContent().
     * THIS OVERWRITE ALL CONTENT, to add more content use pageAddContent($value).
     */
    public function pageSetContent(?string $value): void
    {
        if ( ! $value)
        {
            return;
        }

        $this->setContent($value);
    }

    /**
     * Alias of getContent.
     */
    public function pageGetContent(): string
    {
        return $this->getContent();
    }

    /**
     * Lets you display debug output (specially formatted, optionally only visible to SU_DEBUG users).
     *
     * @param $text The input text or variable to debug, string
     * @param $force Default is false, if true it will always be outputted to ANY user. If false, only SU_DEBUG will see it.
     */
    public function pageDebug($text, $force = false)
    {
        global $session;

        if ($force || isset($session['user']['superuser']) && $session['user']['superuser'] & SU_DEBUG_OUTPUT)
        {
            $this->pageAddContent(\Tracy\Debugger::dump($text, true));
        }
    }

    /**
     * Page and send content to browser.
     *
     * @param bool $saveuser
     */
    public function pageEnd($saveuser = true): void
    {
        global $session;

        $script = $this->request->getServer('SCRIPT_NAME');
        $script = \substr($script, 0, \strpos($script, '.'));
        $module = (string) $this->request->getQuery('module');

        $args = new EveryRequest(['script' => $script, '__scriptfile__' => $script, 'module' => $module]);
        $this->eventDispatcher->dispatch($args, EveryRequest::FOOTER);
        $args->setData(modulehook('everyfooter', $args->getData()));

        if ($session['user']['loggedin'] ?? false)
        {
            $this->eventDispatcher->dispatch($args, EveryRequest::FOOTER_AUTHENTICATED);
            $args->setData(modulehook('everyfooter-loggedin', $args->getData()));
        }

        $replacementbits = $args->getData();
        unset($replacementbits['__scriptfile__'], $replacementbits['script']);
        //output any template part replacements that above hooks need (eg, advertising)º
        foreach ($replacementbits as $key => $val)
        {
            $content = $this->params->get($key, '').$val;
            $this->params->set($key, $content);
        }

        $session['user']['name']  = $session['user']['name']  ?? '';
        $session['user']['login'] = $session['user']['login'] ?? '';

        //-- START - Check if see or not MoTD
        $lastMotd = new \DateTime('0000-00-00 00:00:00');

        if ($this->doctrine->isConnected())
        {
            $lastMotd = $this->doctrine->getRepository('LotgdCore:Motd')->getLastMotdDate();
        }
        $session['needtoviewmotd'] = $session['needtoviewmotd'] ?? false;

        if (isset($session['user']['lastmotd'])
            && ($lastMotd > $session['user']['lastmotd'])
            && (isset($session['user']['loggedin']) && $session['user']['loggedin'])
        ) {
            $session['needtoviewmotd'] = true;
        }
        //-- END - Check if see or not MoTD

        //-- Character Stats
        $charstats = $this->getOutputCharacterStats();

        $user   = $session['user'] ?? [];
        $sesion = $session         ?? [];
        unset($sesion['user'], $user['password']);

        $this->params
            ->set('stats', $charstats) //-- Output character stats
            ->set('content', $this->getContent()) //-- Set content
        ;
        //-- Twig Globals
        $this->template->addGlobal('user', $user); //-- Update user info
        $this->template->addGlobal('session', $sesion); //-- Update session info

        //-- output page generation time
        $gentime = \Tracy\Debugger::timer('page_footer');
        $session['user']['gentime'] += $gentime;
        ++$session['user']['gentimecount'];

        $browserOutput = $this->template->render('layout.html.twig', $this->params->toArray());

        $session['user']['gensize'] += \strlen($browserOutput);
        $session['output'] = $browserOutput;

        if (true === $saveuser)
        {
            saveuser();
        }

        unset($session['output']);

        $this->doctrine->flush();
        $this->doctrine->clear();

        $this->setContent($browserOutput);
        $this->prepare($this->request); //-- Fix any incompatibility with the HTTP specification

        //-- Send content to browser
        $this->send();

        exit;
    }

    /**
     * Send a cookie.
     *
     * @param string $name
     * @param string $value
     * @param string $duration
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     */
    public function setCookie($name, $value, $duration = '+120 days', $path = '', $domain = '', $secure = true, $httponly = true)
    {
        $this->request->cookies->set($name, $value);
        $this->headers->setCookie(Cookie::create($name, $value, \strtotime($duration), $path, $domain, $secure, $httponly));
    }

    /**
     * Output character stats.
     *
     * @return mixed
     */
    private function getOutputCharacterStats()
    {
        restore_buff_fields();
        calculate_buff_fields();

        $charstats = charstats();
        restore_buff_fields();

        return $charstats;
    }
}
