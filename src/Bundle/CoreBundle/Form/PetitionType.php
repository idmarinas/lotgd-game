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

namespace Lotgd\Bundle\CoreBundle\Form;

use Laminas\Filter;
use Lotgd\Bundle\CoreBundle\Form\Type\PetitionTypesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PetitionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('character', TextType::class, [
                'label'       => 'character',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 100]),
                ],
                'filters' => [
                    new Filter\StripTags(),
                    new Filter\StripNewlines(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'email',
                'constraints' => [
                    new Assert\Email(),
                ],
            ])
            ->add('problem_type', PetitionTypesType::class, [
                'label'     => 'petition.type',
                'petitions' => $options['petitions'],
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'description',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 65000]),
                ],
                'filters' => [
                    new Filter\StripTags(),
                ],
            ])

        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'petitions'          => [],
            'translation_domain' => 'lotgd_core_form_petition',
        ]);
    }
}
