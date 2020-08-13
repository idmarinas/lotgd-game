<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\Form\Type;

use Lotgd\Core\Form\EventListener\AddTranslatableFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatableFieldType extends AbstractType
{
    protected const LABEL_CLASS = 'item disabled';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ( ! class_exists($options['personal_translation']))
        {
            throw new \InvalidArgumentException(sprintf("Unable to find personal translation class: '%s'", $options['personal_translation']));
        }

        if ( ! $options['field'])
        {
            throw new \InvalidArgumentException('You should provide a field to translate');
        }

        $subscriber = new AddTranslatableFieldSubscriber($builder->getFormFactory(), $options);
        $builder->addEventSubscriber($subscriber);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['label_attr']))
        {
            $options['label_attr']['class'] = ($options['label_attr']['class'] ?? '').static::LABEL_CLASS;
        }
        else
        {
            $options['label_attr'] = ['class' => static::LABEL_CLASS];
        }

        $view->vars['label_attr']      = $options['label_attr'];
        $view->vars['locales']         = $options['locales'];
        $view->vars['required_locale'] = $options['required_locale'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'remove_empty'           => true, //Personal Translations without content are removed
            'property_path'          => 'translations',
            'csrf_protection'        => false,
            'personal_translation'   => false, //Personal Translation class
            'locales'                => explode(',', getsetting('serverlanguages')), //the locales you wish to edit
            'required_locale'        => [getsetting('defaultlanguage')], //the required locales cannot be blank
            'field'                  => false, //the field that you wish to translate
            'widget'                 => PersonalTranslationType::class, //change this to another widget like 'texarea' if needed
            'entity_manager_removal' => true, //auto removes the Personal Translation thru entity manager
        ]);

        return $resolver;
    }

    public function getBlockPrefix(): string
    {
        return 'translatable';
    }

    public function getName()
    {
        return 'translatable_field';
    }
}
