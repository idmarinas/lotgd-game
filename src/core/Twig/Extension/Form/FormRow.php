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
use Zend\Form\Exception;

class FormRow extends AbstractElement
{
    const LABEL_APPEND = 'append';
    const LABEL_PREPEND = 'prepend';

    /**
     * The class that is added to element that have errors.
     *
     * @var string
     */
    protected $inputErrorClass = 'error';

    /**
     * The attributes for the row label.
     *
     * @var array
     */
    protected $labelAttributes;

    /**
     * Where will be label rendered?
     *
     * @var string
     */
    protected $labelPosition = self::LABEL_PREPEND;

    /**
     * Are the errors are rendered by this helper?
     *
     * @var bool
     */
    protected $renderErrors = true;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_row', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Utility form helper that renders a label (if it exists), an element and errors.
     *
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(Environment $env, ElementInterface $element, ?string $translatorTextDomain = null): string
    {
        return $env->renderThemeTemplate('form/element/row.twig', [
            'element' => $element,
            'renderErrors' => $this->renderErrors,
            'labelAttributes' => $this->labelAttributes,
            'translatorTextDomain' => $element->getOptions()['translator_text_domain'] ?? $translatorTextDomain
        ]);
    }

    /**
     * Set the class that is added to element that have errors.
     *
     * @param string $inputErrorClass
     *
     * @return FormRow
     */
    public function setInputErrorClass($inputErrorClass)
    {
        $this->inputErrorClass = $inputErrorClass;

        return $this;
    }

    /**
     * Get the class that is added to element that have errors.
     *
     * @return string
     */
    public function getInputErrorClass()
    {
        return $this->inputErrorClass;
    }

    /**
     * Set the attributes for the row label.
     *
     * @param array $labelAttributes
     *
     * @return FormRow
     */
    public function setLabelAttributes($labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    /**
     * Get the attributes for the row label.
     *
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Set the label position.
     *
     * @param string $labelPosition
     *
     * @throws \Zend\Form\Exception\InvalidArgumentException
     *
     * @return FormRow
     */
    public function setLabelPosition($labelPosition)
    {
        $labelPosition = strtolower($labelPosition);

        if (! in_array($labelPosition, [self::LABEL_APPEND, self::LABEL_PREPEND]))
        {
            throw new Exception\InvalidArgumentException(sprintf('%s expects either %s::LABEL_APPEND or %s::LABEL_PREPEND; received "%s"', __METHOD__, __CLASS__, __CLASS__, (string) $labelPosition));
        }
        $this->labelPosition = $labelPosition;

        return $this;
    }

    /**
     * Get the label position.
     *
     * @return string
     */
    public function getLabelPosition()
    {
        return $this->labelPosition;
    }

    /**
     * Set if the errors are rendered by this helper.
     *
     * @param bool $renderErrors
     *
     * @return FormRow
     */
    public function setRenderErrors($renderErrors)
    {
        $this->renderErrors = (bool) $renderErrors;

        return $this;
    }

    /**
     * Retrieve if the errors are rendered by this helper.
     *
     * @return bool
     */
    public function getRenderErrors()
    {
        return $this->renderErrors;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-row';
    }
}
