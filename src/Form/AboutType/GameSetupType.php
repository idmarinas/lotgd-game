<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\AboutType;

use Lotgd\Core\Form\Type\ViewOnlyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameSetupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //-- Enable Slay Other Players
        $builder->add('pvp', ViewOnlyType::class, [
            'label'        => 'game.setup.pvp',
            'apply_filter' => 'yes_no',
        ]);
        //-- Player Fights per day
        $builder->add('pvpday', ViewOnlyType::class, [
            'label'        => 'game.setup.pvpday',
            'apply_filter' => 'numeral',
        ]);
        //-- Days that new players are safe from PvP
        $builder->add('pvpimmunity', ViewOnlyType::class, [
            'label'        => 'game.setup.pvpimmunity',
            'apply_filter' => 'numeral',
        ]);
        //-- Amount of experience when players become killable in PvP
        $builder->add('pvpminexp', ViewOnlyType::class, [
            'label'        => 'game.setup.pvpminexp',
            'apply_filter' => 'numeral',
        ]);
        //-- Clean user posts (filters bad language and splits words over 45 chars long)
        $builder->add('soap', ViewOnlyType::class, [
            'label'        => 'game.setup.soap',
            'apply_filter' => 'yes_no',
        ]);
        $builder->add('newplayerstartgold', ViewOnlyType::class, [
            'label'        => 'game.setup.newplayerstartgold',
            'apply_filter' => 'numeral',
        ]);

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
