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

use Twig\TwigFunction;
use Zend\Form\ElementInterface;

class FormNote extends AbstractElement
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_note', [$this, 'render']),
        ];
    }

    /**
     * Render note for element.
     */
    public function render(ElementInterface $element, ?string $translatorTextDomain = null): string
    {
        $note = $element->getOption('note');

        if (! $note)
        {
            return '';
        }

        $translatorTextDomain = $element->getOption('note_translator_domain') ?: $translatorTextDomain;

        $note = $this->getTranslator()->translate($note, $translatorTextDomain);

        return sprintf('<div class="ui tiny info message">%s</div>', $note);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-note';
    }
}
