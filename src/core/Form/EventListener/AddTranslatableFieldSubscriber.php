<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

class AddTranslatableFieldSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.pre_set_data
        // , form.post_data and form.bind_norm_data event
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT  => 'postBind',
            FormEvents::SUBMIT       => 'bindNormData',
        ];
    }

    public function bindNormData(SubmitEvent $event)
    {
        //Validates the submitted form
        $form    = $event->getForm();
        $options = $form->getConfig()->getOptions();

        foreach ($options['locales'] as $locale)
        {
            $content = $form->get("{$locale}_{$options['field']}")->getData();

            if (
                null === $content && \in_array($locale, $options['required_locale']))
            {
                $form->addError(new FormError(\sprintf("Field '%s' for locale '%s' cannot be blank", $options['field'], $locale)));
            }
        }
    }

    public function postBind(PostSubmitEvent $event)
    {
        //if the form passed the validattion then set the corresponding Personal Translations
        $form    = $event->getForm();
        $data    = $form->getData();
        $options = $form->getConfig()->getOptions();

        $entity = $form->getParent()->getData();

        foreach ($options['locales'] as $locale)
        {
            $translation = $form->get("{$locale}_{$options['field']}")->getData();
            $translation->setObject($entity);

            //Delete the Personal Translation if its empty
            if ( ! $translation->getContent() && $options['remove_empty'])
            {
                $data->removeElement($translation);

                continue;
            }

            //add it to entity
            $entity->addTranslation($translation);

            if ( ! $data->contains($translation))
            {
                $data->add($translation);
            }
        }
    }

    public function preSetData(PreSetDataEvent $event)
    {
        //Builds the custom 'form' based on the provided locales
        $data    = $event->getData();
        $form    = $event->getForm();
        $options = $form->getConfig()->getOptions();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. We're only concerned with when
        // setData is called with an actual Entity object in it (whether new,
        // or fetched with Doctrine). This if statement let's us skip right
        // over the null condition.
        if (null === $data)
        {
            return;
        }

        foreach ($this->bindTranslations($data, $options) as $binded)
        {
            $original = \method_exists($data, 'getOwner') ? $data->getOwner()->{'get'.\ucfirst($options['field'])}() : '';
            $content  = $binded['translation']->getContent() ?: $original;

            $binded['translation']->setContent($content);

            if (\method_exists($data, 'getOwner'))
            {
                $binded['translation']->setObject($data->getOwner());
            }

            $form->add($this->factory->createNamed(
                $binded['fieldName'],
                $options['widget'],
                $binded['translation'],
                [
                    'label'    => $binded['locale'],
                    'required' => \in_array($binded['locale'], $options['required_locale']),
                    // 'property_path' => 'content',
                    'auto_initialize' => false,
                ]
            ));
        }
    }

    private function bindTranslations($data, $options)
    {
        //Small helper function to extract all Personal Translation
        //from the Entity for the field we are interested in
        //and combines it with the fields

        $collection            = [];
        $availableTranslations = [];

        foreach ($data as $Translation)
        {
            if (\strtolower($Translation->getField()) === \strtolower($options['field']))
            {
                $availableTranslations[\strtolower($Translation->getLocale())] = $Translation;
            }
        }

        foreach ($options['locales'] as $locale)
        {
            if (isset($availableTranslations[\strtolower($locale)]))
            {
                $Translation = $availableTranslations[\strtolower($locale)];
            }
            else
            {
                $Translation = $this->createPersonalTranslation($locale, $options['field'], null, $options);
            }

            $collection[] = [
                'locale'      => $locale,
                'fieldName'   => "{$locale}_{$options['field']}",
                'translation' => $Translation,
            ];
        }

        return $collection;
    }

    private function createPersonalTranslation($locale, $field, $content, $options)
    {
        //creates a new Personal Translation
        $className = $options['personal_translation'];

        return new $className($locale, $field, $content);
    }
}
