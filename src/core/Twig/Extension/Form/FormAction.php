<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Twig\Extension\Form;

use Lotgd\Core\Template\Theme as Environment;
use Twig\TwigFunction;

class FormAction extends AbstractElement
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_action', [$this, 'render'], ['needs_environment' => true]),
            new TwigFunction('form_action_row', [$this, 'renderRow'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render a form actions.
     */
    public function render(Environment $env, array $actions): string
    {
        $params = $this->getActionParams($actions);

        return $env->renderThemeTemplate('form/action/button.twig', $params);
    }

    /**
     * Render a form actions in row.
     */
    public function renderRow(Environment $env, array $actions): string
    {
        $params = $this->getActionParams($actions);

        return $env->renderThemeTemplate('form/action/row.twig', $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-action';
    }

    private function getActionParams(array $actions)
    {
        $submit = $actions['submit'] ?? null;
        $reset = $actions['reset'] ?? null;

        unset($actions['submit'], $actions['reset']);

        return [
            'submit' => $submit,
            'reset' => $reset,
            'buttons' => $actions,
        ];
    }
}
