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

class NewdayType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //-- Player must have fewer than how many forest fights to earn interest?
            ->add('fightsforinterest', ViewOnlyType::class, [
                'label'        => 'newday.fightsforinterest',
                'apply_filter' => 'numeral',
            ])
            //-- Max Interest Rate (%)
            ->add('maxinterest', ViewOnlyType::class, [
                'label'        => 'newday.maxinterest',
                'apply_filter' => 'numeral',
            ])
            //-- Min Interest Rate (%)
            ->add('mininterest', ViewOnlyType::class, [
                'label'        => 'newday.mininterest',
                'apply_filter' => 'numeral',
            ])
            // Game days per calendar day
            ->add('daysperday', ViewOnlyType::class, [
                'label'        => 'newday.daysperday',
                'apply_filter' => 'numeral',
            ])
            // Extra daily uses in specialty area
            ->add('specialtybonus', ViewOnlyType::class, [
                'label'        => 'newday.specialtybonus',
                'apply_filter' => 'numeral',
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
