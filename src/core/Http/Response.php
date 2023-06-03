<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Http;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\View\Helper\HeadTitle;
use Lotgd\Core\Combat\Buffer;
use Lotgd\Core\Doctrine\ORM\EntityManager;
use Lotgd\Core\Event\EveryRequest;
use Lotgd\Core\Kernel;
use Lotgd\Core\Service\PageParts;
use Lotgd\Core\Template\Params;
use Lotgd\Core\Tool\Tool;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tracy\Debugger;
use Twig\Environment;

class Response extends HttpResponse
{
    private $translator;
    private EntityManager $doctrine;
    private $headTitle;
    private $template;
    private $request;
    private $params;
    private EventDispatcherInterface $eventDispatcher;
    private $kernel;
    private $pageParts;
    private Buffer $buffer;
    private Tool $tool;

    public function __construct(
        EntityManagerInterface $doctrine,
        HeadTitle $headTitle,
        Environment $template,
        Request $request,
        Params $params,
        KernelInterface $kernel,
        PageParts $pageParts
    ) {
        $this->translator      = $kernel->getContainer()->get('translator');
        $this->eventDispatcher = $kernel->getContainer()->get('event_dispatcher');
        $this->doctrine        = $doctrine;
        $this->headTitle       = $headTitle;
        $this->template        = $template;
        $this->request         = $request;
        $this->params          = $params;
        $this->kernel          = $kernel;
        $this->pageParts       = $pageParts;
        $this->buffer          = $this->kernel->getContainer()->get('lotgd_core.combat.buffer');
        $this->tool            = $this->kernel->getContainer()->get('lotgd.core.tools');

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
        { // -- If not have title not overwrite page title
            $this->pageTitle($title, $parameters, $textDomain, $locale);
        }

        $script = $this->request->getServer('SCRIPT_NAME');
        $script = substr($script, 0, strpos($script, '.'));
        $module = (string) $this->request->getQuery('module');

        $args = new EveryRequest(['script' => $script, 'module' => $module]);
        $this->eventDispatcher->dispatch($args, EveryRequest::HEADER);
        $args->setData(modulehook('everyheader', $args->getData()));

        if ($session['user']['loggedin'] ?? false)
        {
            $this->eventDispatcher->dispatch($args, EveryRequest::HEADER_AUTHENTICATED);
            modulehook('everyheader-loggedin', $args->getData());
        }

        $this->buffer->calculateBuffFields();

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
            $this->pageAddContent(Debugger::dump($text, true));
        }
    }

    /**
     * Call a controller and add content to response.
     *
     * @param string $class  Fully Qualified Class Name
     * @param string $method Method to call of controller
     * @param bool   $send   Send content or add to response content
     */
    public function callController(
        string $class,
        string $method = 'index',
        bool $send = false,
        bool $saveUser = true
    ): void {
        $resolver   = new ArgumentResolver();
        $controller = [$this->kernel->getContainer()->get($class), $method];

        // -- Controller arguments
        $arguments = $resolver->getArguments($this->request, $controller);

        $response = $controller(...$arguments);

        // -- If is a instance of RedirectResponse|BinaryFileResponse|JsonResponse send response.
        if (
            $send
            || $response instanceof RedirectResponse
            || $response instanceof BinaryFileResponse
            || $response instanceof JsonResponse
        ) {
            if ($saveUser) {
                $this->tool->saveUser();
            }

            $response->send();

            exit;
        }

        $this->pageAddContent($response->getContent());
    }

    /**
     * Page and send content to browser.
     *
     * @param bool $saveuser
     *
     * @return never
     */
    public function pageEnd($saveuser = true): void
    {
        global $session;

        $script = $this->request->getServer('SCRIPT_NAME');
        $script = substr($script, 0, strpos($script, '.'));
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
        // output any template part replacements that above hooks need (eg, advertising)º
        foreach ($replacementbits as $key => $val)
        {
            $content = $this->params->get($key, '').$val;
            $this->params->set($key, $content);
        }

        $session['user']['name']  ??= '';
        $session['user']['login'] ??= '';

        // -- START - Check if see or not MoTD
        $lastMotd = new DateTime('0000-00-00 00:00:00');

        if ($this->doctrine->isConnected())
        {
            $lastMotd = $this->doctrine->getRepository('LotgdCore:Motd')->getLastMotdDate();
        }
        $session['needtoviewmotd'] ??= false;

        if (isset($session['user']['lastmotd'])
            && ($lastMotd > $session['user']['lastmotd'])
            && (isset($session['user']['loggedin']) && $session['user']['loggedin'])
        ) {
            $session['needtoviewmotd'] = true;
        }
        // -- END - Check if see or not MoTD

        // -- Character Stats
        $charstats = $this->getOutputCharacterStats();

        $user   = $session['user'] ?? [];
        $sesion = $session         ?? [];
        unset($sesion['user'], $user['password']);

        $this->params
            ->set('stats', $charstats) // -- Output character stats
            ->set('content', $this->getContent()) // -- Set content
        ;
        // -- Twig Globals
        $this->template->addGlobal('user', $user); // -- Update user info
        $this->template->addGlobal('session', $sesion); // -- Update session info

        // -- output page generation time
        $gentime = Debugger::timer('page_footer');
        $session['user']['gentime'] += $gentime;
        ++$session['user']['gentimecount'];

        $browserOutput = $this->template->render('layout.html.twig', $this->params->toArray());

        $session['user']['gensize'] += \strlen($browserOutput);
        $session['output'] = $browserOutput;

        if ($saveuser)
        {
            $this->tool->saveUser(true, false);
        }

        unset($session['output']);

        $this->doctrine->flush();
        $this->doctrine->clear();

        $this->setContent($browserOutput);
        $this->prepare($this->request); // -- Fix any incompatibility with the HTTP specification

        // -- Send content to browser
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
        $this->headers->setCookie(Cookie::create($name, $value, strtotime($duration), $path, $domain, $secure, $httponly));
    }

    /**
     * Output character stats.
     *
     * @return mixed
     */
    private function getOutputCharacterStats()
    {
        $this->buffer->restoreBuffFields();
        $this->buffer->calculateBuffFields();

        $charstats = $this->pageParts->charStats();
        $this->buffer->restoreBuffFields();

        return $charstats;
    }
}
