<?php
/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Ajax\Pattern\Core\Petition;

use Jaxon\Response\Response;
use Lotgd\Core\Event\Core;
use Tracy\Debugger;

trait Faq
{
    public function faq(?int $faq = null)
    {
        $response = new Response();
        $params   = $this->getParams();

        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title.faq', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app_default'),
                    'class' => 'ui red deny button',
                ],
            ];

            //-- Options
            $options = [
                'autofocus' => false,
            ];

            if ($faq)
            {
                $faq     = \max(1, \min(4, $faq));
                $content = $this->getTemplate()->renderBlock("petition_faq{$faq}", $params);
            }
            else
            {
                $params['faqList'] = $this->faqToc();

                // Dialog content
                $content = $this->getTemplate()->renderBlock('petition_faq', $params);
            }

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);
        }

        $response->jQuery('#village-petition-faq')->removeClass('disabled');

        return $response;
    }

    public function primer()
    {
        $response = new Response();
        $params   = $this->getParams();

        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title.faq', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app_default'),
                    'class' => 'ui red deny button',
                ],
            ];

            //-- Options
            $options = [
                'autofocus' => false,
            ];

            $params['pvp']           = \LotgdSetting::getSetting('pvp', 1);
            $params['deathOverlord'] = \LotgdSetting::getSetting('deathoverlord', '`$Ramius`0');
            $params['pvpImmunity']   = \LotgdSetting::getSetting('pvpimmunity', 5);
            $params['pvpMinExp']     = \LotgdSetting::getSetting('pvpminexp', 1500);
            $params['pvpDeflose']    = \LotgdSetting::getSetting('pvpdeflose', 5);
            $params['pvpAttGain']    = \LotgdSetting::getSetting('pvpattgain', 10);
            $params['pvpAttLose']    = \LotgdSetting::getSetting('pvpattlose', 15);
            $params['pvpDefGain']    = \LotgdSetting::getSetting('pvpdefgain', 10);

            // Dialog content
            $content = $this->getTemplate()->renderBlock('petition_primer', $params);

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);
        }

        $response->jQuery('#village-petition-faq')->removeClass('disabled');

        return $response;
    }

    /**
     * Creaqte list of faqs.
     */
    private function faqToc(): array
    {
        $args = new Core([
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.primer()',
                'link'    => [
                    'section.faq.toc.primer',
                    [],
                    $this->getTextDomain(),
                ],
            ],
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.faq(1)',
                'link'    => [
                    'section.faq.toc.general',
                    [],
                    $this->getTextDomain(),
                ],
            ],
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.faq(2)',
                'link'    => [
                    'section.faq.toc.spoiler',
                    [],
                    $this->getTextDomain(),
                ],
            ],
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.faq(3)',
                'link'    => [
                    'section.faq.toc.technical',
                    [],
                    $this->getTextDomain(),
                ],
            ],
        ]);
        \LotgdEventDispatcher::dispatch($args, Core::PETITION_FAQ_TOC);

        return modulehook('faq-toc', $args->getData());
    }
}
