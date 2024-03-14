<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Ajax\Core;

use Doctrine;
use Jaxon\Response\Response;
use Lotgd\Core\AjaxAbstract;
use LotgdFormat;
use LotgdTranslator;
use Throwable;
use Tracy\Debugger;
use function Jaxon\jaxon;

class Mounts extends AjaxAbstract
{
    public function getListOfOwners(int $mountId)
    {
        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
        }

        $response = jaxon()->getResponse();

        try
        {
            $repository = Doctrine::getRepository('LotgdCore:Avatar');
            $entities   = $repository->findBy(['hashorse' => $mountId]);
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            $entities = [];
        }
        // The dialog buttons
        $buttons = [
            [
                'title' => LotgdTranslator::t('modal.buttons.cancel', [], 'app_default'),
                'class' => 'ui red deny button',
            ],
        ];

        $content = '';

        foreach ($entities as $char)
        {
            $content .= LotgdFormat::colorize($char->getName(), true).'<br>';
        }

        // Show the dialog
        $response->dialog->show('', ['content' => $content ?: '---'], $buttons);

        return $response;
    }
}
