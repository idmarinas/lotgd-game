<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\EventListener;

use Laminas\View\Helper\HeadTitle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PageListener
{
    protected $params;
    protected $headTitle;
    protected $request;
    protected $environment;

    public function __construct(ParameterBagInterface $params, HeadTitle $headTitle, RequestStack $request, Environment $environment)
    {
        $this->params      = $params;
        $this->headTitle   = $headTitle;
        $this->request     = $request->getCurrentRequest();
        $this->environment = $environment;
    }

    public function onKernelRequest()
    {
        //-- Define Twig global variable for text_domain
        $controller = \preg_replace('/::.+/', '', $this->request->get('_controller')).'::TEXT_DOMAIN';
        $textDomain = \defined($controller) ? $this->environment->addGlobal('text_domain', \constant($controller)) : null;
        $this->environment->addGlobal('text_domain', $textDomain);

        //-- Add default page title
        $this->headTitle->__invoke($this->params->get('lotgd.core.seo.title.default'), 'SET');
    }
}
