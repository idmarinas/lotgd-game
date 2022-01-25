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

namespace Lotgd\Core\Form\Type;

use LotgdEventDispatcher;
use LotgdSanitize;
use LotgdTranslator;
use Lotgd\Core\Event\Clan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClanRankType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        //Inserted for v1.1.0 Dragonprime Edition to extend clan possibilities
        $ranks = [
            CLAN_APPLICANT      => 'ranks.00',
            CLAN_MEMBER         => 'ranks.010',
            CLAN_OFFICER        => 'ranks.020',
            CLAN_ADMINISTRATIVE => 'ranks.025',
            CLAN_LEADER         => 'ranks.030',
            CLAN_FOUNDER        => 'ranks.031',
        ];
        $ranksResult = new Clan(['ranks' => $ranks, 'textDomain' => 'page_clan', 'clanid' => null]);
        LotgdEventDispatcher::dispatch($ranksResult, Clan::RANK_LIST);
        $ranksResult = modulehook('clanranks', $ranksResult->getData());
        $ranks       = $ranksResult['ranks'];

        $choices = [];

        foreach ($ranks as $rankId => $rankName)
        {
            $choices[LotgdSanitize::fullSanitize(LotgdTranslator::t($rankName, [], $ranksResult['textDomain']))] = $rankId;
        }

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $choices,
        ]);

        return $resolver;
    }
}
