<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat;

use Lotgd\Core\ExpressionLanguage\Battle as ExpressionLanguageBattle;
use Lotgd\Core\Http\Response;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\Sanitize;
use Lotgd\Core\Tool\Tool;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

trait BattleTrait
{
    private $tools;
    private $log;
    private $navigation;
    private $response;
    private $twig;
    private $sanitize;
    private $translator;
    private $expression;

    /**
     * @required
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @required
     */
    public function setSanitize(Sanitize $sanitize): void
    {
        $this->sanitize = $sanitize;
    }

    /**
     * @required
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @required
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @required
     */
    public function setNavigation(Navigation $navigation): void
    {
        $this->navigation = $navigation;
    }

    /**
     * @required
     */
    public function setLog(Log $log): void
    {
        $this->log = $log;
    }

    /**
     * @required
     */
    public function setTools(Tool $tools): void
    {
        $this->tools = $tools;
    }

    public function setExpression(ExpressionLanguageBattle $expression): void
    {
        $this->expression = $expression;
    }
}
