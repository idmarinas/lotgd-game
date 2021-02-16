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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Days to keep comments and news?  (0 = infinite)
            ->add('expirecontent', NumberType::class, [
                'required'    => false,
                'empty_data'  => 180,
                'label'       => 'content.expirecontent',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Days to keep the debuglog? (0=infinite)
            ->add('expiredebuglog', NumberType::class, [
                'required'    => false,
                'empty_data'  => 18,
                'label'       => 'content.expiredebuglog',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Days to keep the faillog? (0=infinite)
            ->add('expirefaillog', NumberType::class, [
                'required'    => false,
                'empty_data'  => 15,
                'label'       => 'content.expirefaillog',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Days to keep the gamelog? (0=infinite)
            ->add('expiregamelog', NumberType::class, [
                'required'    => false,
                'empty_data'  => 30,
                'label'       => 'content.expiregamelog',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Days to keep never logged-in accounts? (0 = infinite)
            ->add('expiretrashacct', NumberType::class, [
                'required'    => false,
                'empty_data'  => 1,
                'label'       => 'content.expiretrashacct',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Days to keep 1 level (0 dragon) accounts? (0 =infinite)
            ->add('expirenewacct', NumberType::class, [
                'required'    => false,
                'empty_data'  => 10,
                'label'       => 'content.expirenewacct',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Notify the user how many days before expiration via email
            ->add('expirenotificationdays', NumberType::class, [
                'required'    => false,
                'empty_data' => 0,
                'label'       => 'content.expirenotificationdays.label',
                'help'        => 'content.expirenotificationdays.note',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Days to keep all other accounts? (0 = infinite)
            ->add('expireoldacct', NumberType::class, [
                'required'    => false,
                'empty_data'  => 45,
                'label'       => 'content.expireoldacct',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Seconds of inactivity before auto-logoff
            ->add('LOGINTIMEOUT', NumberType::class, [
                'required'    => false,
                'empty_data'  => 900,
                'label'       => 'content.LOGINTIMEOUT',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
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
