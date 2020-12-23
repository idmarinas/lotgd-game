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

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Laminas\View\Helper\HeadTitle;
use Lotgd\Core\Hook;
use Lotgd\Core\Pattern;
use Lotgd\Core\Translator\Translator;

class Response extends HttpResponse
{
    use Pattern\Container;
    use Pattern\Http;
    use Pattern\HookManager;
    use Pattern\Template;
    use Pattern\Doctrine;
    use Pattern\Translator;

    /**
     * Set title for page.
     */
    public function pageTitle(string $message, ?array $parameters = [], string $textDomain = Translator::TEXT_DOMAIN_DEFAULT, ?string $locale = null): void
    {
        $title     = $this->getTranslator()->trans($message, $parameters, $textDomain, $locale);
        $headTitle = $this->getContainer(HeadTitle::class);

        $headTitle($title, 'set');
    }

    /**
     * Start page.
     */
    public function pageStart(?string $title = null, ?array $parameters = [], string $textDomain = Translator::TEXT_DOMAIN_DEFAULT, ?string $locale = null): void
    {
        global $session;

        if ($title)
        { //-- If not have title not overwrite page title
            $this->pageTitle($title, $parameters, $textDomain, $locale);
        }

        $script = \LotgdRequest::getServer('SCRIPT_NAME');
        $script = \substr($script, 0, \strpos($script, '.'));
        $module = (string) \LotgdRequest::getQuery('module');

        $args = ['script' => $script, 'module' => $module];
        $this->getHookManager()->trigger(Hook::HOOK_EVERY_HEADER, null, $args);
        $args = modulehook('everyheader', $args); //-- This hook will be removed in version 5.0.0

        if ($session['user']['loggedin'] ?? false)
        {
            $this->getHookManager()->trigger(Hook::HOOK_EVERY_HEADER_AUTHENTICATED, null, $args);
            modulehook('everyheader-loggedin', $args); //-- This hook will be removed in version 5.0.0
        }

        calculate_buff_fields();

        $userPre   = $session['user'] ?? [];
        $sesionPre = $session         ?? [];
        unset($sesionPre['user'], $userPre['password']);

        $this->getTemplate()->addGlobal('userPre', $userPre);
        $this->getTemplate()->addGlobal('sessionPre', $sesionPre);
    }

    /**
     * Add content to response.
     * To overwrite content use pageSetContent($value) or setContent($value).
     */
    public function pageAddContent(?string $value): void
    {
        if (! $value)
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
        if (! $value)
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
    public function pageDebug(?string $text, $force = false)
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

        $script = \LotgdRequest::getServer('SCRIPT_NAME');
        $script = \substr($script, 0, \strpos($script, '.'));
        $module = (string) \LotgdRequest::getQuery('module');

        $replacementbits = ['script' => $script, '__scriptfile__' => $script, 'module' => $module];
        $this->getHookManager()->trigger(Hook::HOOK_EVERY_FOOTER, null, $replacementbits);
        $replacementbits = modulehook('everyfooter', $replacementbits); //-- This hook will be removed in version 5.0.0

        if ($session['user']['loggedin'] ?? false)
        {
            $this->getHookManager()->trigger(Hook::HOOK_EVERY_FOOTER_AUTHENTICATED, null, $replacementbits);
            $replacementbits = modulehook('everyfooter-loggedin', $replacementbits); //-- This hook will be removed in version 5.0.0
        }

        unset($replacementbits['__scriptfile__'], $replacementbits['script']);
        //output any template part replacements that above hooks need (eg, advertising)º
        foreach ($replacementbits as $key => $val)
        {
            $content = $this->getTemplateParams()->get($key, '').$val;
            $this->getTemplateParams()->set($key, $content);
        }

        $session['user']['name']  = $session['user']['name']  ?? '';
        $session['user']['login'] = $session['user']['login'] ?? '';

        //-- START - Check if see or not MoTD
        $lastMotd = new \DateTime('0000-00-00 00:00:00');

        if ($this->getDoctrine()->isConnected())
        {
            $lastMotd = $this->getDoctrine()->getRepository('LotgdCore:Motd')->getLastMotdDate();
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

        $this->getTemplateParams()
            ->set('stats', $charstats) //-- Output character stats
            ->set('content', $this->getContent()) //-- Set content
        ;
        //-- Twig Globals
        $this->getTemplate()->addGlobal('user', $user); //-- Update user info
        $this->getTemplate()->addGlobal('session', $sesion); //-- Update session info

        //-- output page generation time
        $gentime = \Tracy\Debugger::timer('page-footer');
        $session['user']['gentime'] += $gentime;
        ++$session['user']['gentimecount'];

        $browserOutput = $this->getTemplate()->renderLayout($this->getTemplateParams()->toArray());

        $session['user']['gensize'] += \strlen($browserOutput);
        $session['output'] = $browserOutput;

        if (true === $saveuser)
        {
            saveuser();
        }

        unset($session['output']);

        $this->getDoctrine()->flush();
        $this->getDoctrine()->clear();

        $this->setContent($browserOutput);
        $this->prepare(\LotgdRequest::_i()); //-- Fix any incompatibility with the HTTP specification

        //-- Send content to browser
        $this->send();

        exit();
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
