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
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LogdnetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Register with LoGDnet?
            ->add('logdnet', CheckboxType::class, [
                'required' => false,
                'label'    => 'logdnet.logdnet.label',
                'help'     => 'logdnet.logdnet.note',
            ])
            // Server Description (75 chars max)
            ->add('serverdesc', TextType::class, [
                'required'    => false,
                'label'       => 'logdnet.serverdesc',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 75]),
                ],
            ])
            // Master LoGDnet Server (default http://logdnet.logd.com/)
            ->add('logdnetserver', TextType::class, [
                'required'    => false,
                'label'       => 'logdnet.logdnetserver',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 255]),
                ],
            ])
            // How long we wait for responses from that server (in seconds)
            ->add('curltimeout', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                ],
                'empty_data'  => 2,
                'label'       => 'logdnet.curltimeout',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\Positive(),
                    new Assert\Range(['min' => 1, 'max' => 10]),
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
