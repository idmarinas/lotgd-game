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

class MiscType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //-- Day Duration
            ->add('dayduration', ViewOnlyType::class, [
                'label' => 'info.dayduration',
            ])
            //-- Current game time
            ->add('curgametime', ViewOnlyType::class, [
                'label' => 'info.curgametime',
            ])
            //-- Current Server Time
            ->add('curservertime', ViewOnlyType::class, [
                'label' => 'info.curservertime',
            ])
            //-- Last new day
            ->add('lastnewday', ViewOnlyType::class, [
                'label' => 'info.lastnewday',
            ])
            //-- Next new day
            ->add('nextnewday', ViewOnlyType::class, [
                'label' => 'info.nextnewday',
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
