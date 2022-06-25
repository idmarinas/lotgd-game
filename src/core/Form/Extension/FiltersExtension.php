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

namespace Lotgd\Core\Form\Extension;

use Laminas\Filter\FilterInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltersExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event)
        {
            $filters = $event->getForm()->getConfig()->getOption('filters');
            $data = $event->getData();

            foreach ($filters as $filter)
            {
                $data = $filter->filter($data);
            }

            $event->setData($data);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        //-- Filters should always be converted to an array
        //-- Filter need be instance of
        $filtersNormalizer = function (Options $options, $filters)
        {
            $filters = \is_object($filters) ? [$filters] : (array) $filters;

            foreach ($filters as $key => $filter)
            {
                if ($filter instanceof FilterInterface)
                {
                    continue;
                }

                //-- Delete filters that not implements FilterInterface
                unset($filters[$key]);
            }

            return $filters;
        };

        $resolver->setDefaults([
            'filters' => [],
        ]);

        $resolver->setNormalizer('filters', $filtersNormalizer);
    }

    /**
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
