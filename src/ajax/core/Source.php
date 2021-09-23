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

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;
use Lotgd\Core\AjaxAbstract;
use Tracy\Debugger;

/**
 * Dialog for Source.
 */
class Source extends AjaxAbstract
{
    public const TEXT_DOMAIN = 'jaxon_source';

    private $templatePetition;

    public function show(): Response
    {
        $response = new Response();

        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title', [], self::TEXT_DOMAIN);

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app_default'),
                    'class' => 'ui red deny button',
                ],
            ];

            $params = [
                'textDomain' => self::TEXT_DOMAIN,
            ];

            $content = $this->getTemplate()->renderBlock('source_show', $params);

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('flash.message.error', [], 'app_default'));
        }

        $response->jQuery('#button-source')->removeClass('loading disabled');

        return $response;
    }

    /**
     * Get template of block for Petition.
     */
    protected function getTemplate()
    {
        if ( ! $this->templatePetition)
        {
            $this->templatePetition = \LotgdTheme::load('admin/_blocks/_source.html.twig');
        }

        return $this->templatePetition;
    }
}
