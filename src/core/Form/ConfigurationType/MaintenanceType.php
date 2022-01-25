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

use Laminas\Filter\StripTags;
use Laminas\Filter;
use Lotgd\Core\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MaintenanceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('debug', CheckboxType::class, [
            'required' => false,
            'label'    => 'maintenance.debug.label',
            'help'     => 'maintenance.debug.note',
        ]);
        $builder->add('maintenance', CheckboxType::class, [
            'required' => false,
            'label'    => 'maintenance.maintenance.label',
            'help'     => 'maintenance.maintenance.note',
        ]);
        $builder->add('fullmaintenance', CheckboxType::class, [
            'required' => false,
            'label'    => 'maintenance.fullmaintenance.label',
            'help'     => 'maintenance.fullmaintenance.note',
        ]);
        $builder->add('maintenancenote', TextareaType::class, [
            'required'    => false,
            'label'       => 'maintenance.maintenancenote',
            'empty_data'  => '',
            'constraints' => [
                new Assert\Length(['min' => 0, 'max' => 255, 'allowEmptyString' => true]),
            ],
            'filters' => [
                new StripTags(),
            ],
        ]);
        $builder->add('maintenanceauthor', TextareaType::class, [
            'required'    => false,
            'label'       => 'maintenance.maintenanceauthor',
            'empty_data'  => '',
            'constraints' => [
                new Assert\Length(['min' => 0, 'max' => 255, 'allowEmptyString' => true]),
            ],
            'filters' => [
                new StripTags(),
            ],
        ]);

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
