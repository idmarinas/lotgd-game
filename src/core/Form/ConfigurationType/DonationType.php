<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\ConfigurationType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DonationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Points to award for $1 (or 1 of whatever currency you allow players to donate)
            ->add('dpointspercurrencyunit', NumberType::class, [
                'required'    => false,
                'empty_data'  => 100,
                'label'       => 'donation.dpointspercurrencyunit',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\Positive(),
                ],
            ])
            // Email address of Admin's paypal account
            ->add('paypalemail', EmailType::class, [
                'required'    => false,
                'label'       => 'donation.paypalemail',
                'constraints' => [
                    new Assert\Email(),
                ],
            ])
            // Currency type
            ->add('paypalcurrency', CurrencyType::class, [
                'required' => false,
                'label'    => 'donation.paypalcurrency',
                'attr'     => [
                    'class' => 'search fluid three column',
                ],
                'constraints' => [
                    new Assert\Currency(),
                    new Assert\Length(['min' => 3, 'max' => 100]),
                ],
            ])
            // What country's predominant language do you wish to have displayed in your PayPal screen?
            ->add('paypalcountry-code', CountryType::class, [
                'required' => false,
                'label'    => 'donation.paypalcountry.code',
                'attr'     => [
                    'class' => 'search fluid three column',
                ],
                'constraints' => [
                    new Assert\Country(),
                ],
            ])
            // What text should be displayed as item name in the donations screen(player name will be added after it)?
            ->add('paypaltext', TextType::class, [
                'required'    => false,
                'empty_data'  => 'Legend of the Green Dragon Site Donation from',
                'label'       => 'donation.paypaltext.label',
                'help'        => 'donation.paypaltext.note',
                'constraints' => [
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'form_core_configuration',
        ]);
    }
}
