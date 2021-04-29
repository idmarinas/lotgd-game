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
use Lotgd\Bundle\CoreBundle\Entity\Petition;
use Lotgd\Bundle\CoreBundle\Entity\PetitionType as EntityPetitionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetitionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarName', TextType::class, [
                'label'    => 'avatar_name',
                'required' => false,
                'filters'  => [
                    new Filter\StripTags(),
                    new Filter\StripNewlines(),
                ],
            ])
            ->add('userOfAvatar', TextType::class, [
                'label'   => 'user_of_avatar',
                'filters' => [
                    new Filter\StripTags(),
                    new Filter\StripNewlines(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
            ])
            ->add('problemType', EntityType::class, [
                'label'        => 'petition.type',
                'class'        => EntityPetitionType::class,
                'choice_label' => 'name',
            ])
            ->add('subject', TextType::class, [
                'label'   => 'subject',
                'filters' => [
                    new Filter\StripTags(),
                    new Filter\StripNewlines(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'   => 'description',
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
            'data_class'         => Petition::class,
            'petitions'          => [],
            'translation_domain' => 'lotgd_core_form_petition',
        ]);
    }
}
