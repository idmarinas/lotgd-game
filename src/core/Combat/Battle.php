<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Combat;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Http\Response;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\PlayerFunction;
use Lotgd\Core\Tool\Sanitize;
use Lotgd\Core\Tool\Tool;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Battle
{
    use Battle\Action;
    use Battle\Buff;
    use Battle\Extended;
    use Battle\Skill;

    private $tools;
    private $buffer;
    private $dispatcher;
    private $doctrine;
    private $log;
    private $navigation;
    private $response;
    private $twig;
    private $sanitize;
    private $translator;
    private $playerFunction;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $doctrine,
        Tool $tools,
        Buffer $buffer,
        Log $log,
        Navigation $navigation,
        Response $response,
        Environment $twig,
        Sanitize $sanitize,
        TranslatorInterface $translator,
        PlayerFunction $playerFunction
    ) {
        $this->tools          = $tools;
        $this->buffer         = $buffer;
        $this->dispatcher     = $dispatcher;
        $this->doctrine       = $doctrine;
        $this->log            = $log;
        $this->navigation     = $navigation;
        $this->response       = $response;
        $this->twig           = $twig;
        $this->sanitize       = $sanitize;
        $this->translator     = $translator;
        $this->playerFunction = $playerFunction;
    }
}
