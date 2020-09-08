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

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;
use Lotgd\Core\AjaxAbstract;
use Tracy\Debugger;

/**
 * Dialog for Source.
 */
class Source extends AjaxAbstract
{
    const TEXT_DOMAIN = 'jaxon-source';

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
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                    'class' => 'ui red deny button',
                ],
            ];

            $params = [
                'textDomain' => self::TEXT_DOMAIN
            ];

            $content = \LotgdTheme::renderLotgdTemplate('core/jaxon/source.twig', $params);

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $response->dialog->error(\LotgdTranslator::t('flash.message.error', [], 'app-default'));
        }

        $response->jQuery('#button-source')->removeClass('loading disabled');

        return $response;
    }
}
