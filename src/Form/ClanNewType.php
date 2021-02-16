<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Form;

use Lotgd\Core\Entity\Clans;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClanNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('clanname', TextType::class, ['label' => 'section.applicant.new.form.label.name'])
            ->add('clanshort', TextType::class, ['label' => 'section.applicant.new.form.label.short.name'])

            ->add('save', SubmitType::class, ['label' => 'section.applicant.new.form.button.submit'])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Clans::class,
            'translation_domain' => 'page_clan',
        ]);
    }
}
