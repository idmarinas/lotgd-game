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

namespace Lotgd\Core\Form\ConfigurationType;

use Lotgd\Core\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ClansType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Enable Clan System?
            ->add('allowclans', CheckboxType::class, [
                'required' => false,
                'label' => 'clans.allowclans',
            ])
            // Gold to start a clan
            ->add('goldtostartclan', NumberType::class, [
                'required' => false,
                'empty_data' => 10000,
                'label' => 'clans.goldtostartclan',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero()
                ]
            ])
            // Gems to start a clan
            ->add('gemstostartclan', NumberType::class, [
                'required' => false,
                'empty_data' => 15,
                'label' => 'clans.gemstostartclan',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero()
                ]
            ])
            // Can clan officers who are also moderators moderate their own clan even if they cannot moderate all clans?
            ->add('officermoderate', CheckboxType::class, [
                'required' => false,
                'label' => 'clans.officermoderate',
            ])
            // Hard sanitize for all but latin chars  in the clan name at creation?
            ->add('clannamesanitize', CheckboxType::class, [
                'required' => false,
                'label' => 'clans.clannamesanitize',
            ])
            // Hard sanitizie for all but latin chars in the short name at creation?
            ->add('clanshortnamesanitize', CheckboxType::class, [
                'required' => false,
                'label' => 'clans.clanshortnamesanitize',
            ])
            // Length of the short name (max 20)
            ->add('clanshortnamelength', NumberType::class, [
                'required' => false,
                'empty_data' => 3,
                'label' => 'clans.clanshortnamelength',
                'constraints' => [
                    new Assert\Range(['min' => 3, 'max' => 20])
                ]
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'form-core-grotto-configuration',
        ]);
    }
}
