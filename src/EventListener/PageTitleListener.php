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

class PageTitleListener
{
    protected $params;
    protected $headTitle;

    public function __construct(ParameterBagInterface $params, HeadTitle $headTitle)
    {
        $this->params = $params;
        $this->headTitle = $headTitle;
    }

    public function onKernelRequest()
    {
        //-- Add default page title
        $this->headTitle->__invoke($this->params->get('lotgd.core.seo.title.default'), 'SET');
    }
}
