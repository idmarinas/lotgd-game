<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Twig\Extension\Form;

use Lotgd\Core\Template\Theme as Environment;
use Twig\TwigFunction;
use Zend\Form\ElementInterface;

class FormElementError extends AbstractElement
{
    /**
     * @var array Default attributes for the open format tag
     */
    protected $attributes = [];

    /**
     * @var bool whether or not to translate error messages during render
     */
    protected $translateErrorMessages = true;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_element_error', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render validation errors for the provided $element.
     *
     * If {@link $translateErrorMessages} is true, and a translator is
     * composed, messages retrieved from the element will be translated; if
     * either is not the case, they will not.
     *
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element, array $attributes = [])
    {
        $messages = $element->getMessages();

        if (empty($messages))
        {
            return '';
        }

        $messages = $messages instanceof Traversable ? iterator_to_array($messages) : $messages;

        if (! is_array($messages))
        {
            throw new Exception\DomainException(sprintf('%s expects that $element->getMessages() will return an array or Traversable; received "%s"', __METHOD__, (is_object($messages) ? get_class($messages) : gettype($messages))));
        }

        // Prepare attributes for opening tag
        $attributes = array_merge($this->attributes, $attributes ?? []);
        $attributes = $this->createAttributesString($env, $attributes);

        return $env->renderThemeTemplate('form/element/error.twig', [
            'attributesString' => $attributes,
            'messages' => $messages
        ]);
    }

    /**
     * Set the attributes that will go on the message open format.
     *
     * @param array $attributes key value pairs of attributes
     *
     * @return FormElementErrors
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get the attributes that will go on the message open format.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the flag detailing whether or not to translate error messages.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function setTranslateMessages($flag)
    {
        $this->translateErrorMessages = (bool) $flag;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-element-error';
    }
}
