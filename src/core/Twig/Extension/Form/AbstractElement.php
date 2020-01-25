<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * All files in "Lotgd\Core\Twig\Extension\Form" are based in zend form view classes.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Twig\Extension\Form;

use Lotgd\Core\Pattern as PatternCore;
use Twig\Environment;
use Twig\Extension\AbstractExtension as AbstractExtensionCore;
use Zend\Escaper\Exception\RuntimeException as EscaperException;
use Zend\Form\ElementInterface;
use Zend\Form\Exception\InvalidArgumentException;
use Zend\View\Helper\EscapeHtmlAttr;

/**
 * Base functionality for all form view helpers.
 */
abstract class AbstractElement extends AbstractExtensionCore
{
    use PatternCore\Container;
    use PatternCore\Translator;

    /**
     * The default translatable HTML attributes.
     *
     * @var array
     */
    protected static $defaultTranslatableHtmlAttributes = [
        'title' => true,
    ];

    /**
     * The default translatable HTML attribute prefixes.
     *
     * @var array
     */
    protected static $defaultTranslatableHtmlAttributePrefixes = [];

    /**
     * Standard boolean attributes, with expected values for enabling/disabling.
     *
     * @var array
     */
    protected $booleanAttributes = [
        'autocomplete' => ['on' => 'on',  'off' => 'off'],
        'autofocus' => ['on' => 'autofocus', 'off' => ''],
        'checked' => ['on' => 'checked',   'off' => ''],
        'disabled' => ['on' => 'disabled',  'off' => ''],
        'multiple' => ['on' => 'multiple',  'off' => ''],
        'readonly' => ['on' => 'readonly',  'off' => ''],
        'required' => ['on' => 'required',  'off' => ''],
        'selected' => ['on' => 'selected',  'off' => ''],
    ];

    /**
     * Translatable attributes.
     *
     * @var array
     */
    protected $translatableAttributes = [
        'placeholder' => true,
    ];

    /**
     * Prefixes of translatable HTML attributes.
     *
     * @var array
     */
    protected $translatableAttributePrefixes = [];

    /**
     * @var EscapeHtmlAttr
     */
    protected $escapeHtmlAttrHelper;

    /**
     * Attributes globally valid for all tags.
     *
     * @var array
     */
    protected $validGlobalAttributes = [
        'accesskey' => true,
        'class' => true,
        'contenteditable' => true,
        'contextmenu' => true,
        'dir' => true,
        'draggable' => true,
        'dropzone' => true,
        'hidden' => true,
        'id' => true,
        'lang' => true,
        'onabort' => true,
        'onblur' => true,
        'oncanplay' => true,
        'oncanplaythrough' => true,
        'onchange' => true,
        'onclick' => true,
        'oncontextmenu' => true,
        'ondblclick' => true,
        'ondrag' => true,
        'ondragend' => true,
        'ondragenter' => true,
        'ondragleave' => true,
        'ondragover' => true,
        'ondragstart' => true,
        'ondrop' => true,
        'ondurationchange' => true,
        'onemptied' => true,
        'onended' => true,
        'onerror' => true,
        'onfocus' => true,
        'oninput' => true,
        'oninvalid' => true,
        'onkeydown' => true,
        'onkeypress' => true,
        'onkeyup' => true,
        'onload' => true,
        'onloadeddata' => true,
        'onloadedmetadata' => true,
        'onloadstart' => true,
        'onmousedown' => true,
        'onmousemove' => true,
        'onmouseout' => true,
        'onmouseover' => true,
        'onmouseup' => true,
        'onmousewheel' => true,
        'onpause' => true,
        'onplay' => true,
        'onplaying' => true,
        'onprogress' => true,
        'onratechange' => true,
        'onreadystatechange' => true,
        'onreset' => true,
        'onscroll' => true,
        'onseeked' => true,
        'onseeking' => true,
        'onselect' => true,
        'onshow' => true,
        'onstalled' => true,
        'onsubmit' => true,
        'onsuspend' => true,
        'ontimeupdate' => true,
        'onvolumechange' => true,
        'onwaiting' => true,
        'role' => true,
        'spellcheck' => true,
        'style' => true,
        'tabindex' => true,
        'title' => true,
        'xml:base' => true,
        'xml:lang' => true,
        'xml:space' => true,
    ];

    /**
     * Attribute prefixes valid for all tags.
     *
     * @var array
     */
    protected $validTagAttributePrefixes = [
        'data-',
        'aria-',
        'x-',
    ];

    /**
     * Attributes valid for the tag represented by this helper.
     *
     * This should be overridden in extending classes
     *
     * @var array
     */
    protected $validTagAttributes = [
    ];

    /**
     * Translation text domain.
     *
     * @var string
     */
    protected $translatorTextDomain = 'default';

    /**
     * Create a string of all attribute/value pairs.
     *
     * Escapes all attribute values
     */
    public function createAttributesString(Environment $env, array $attributes): string
    {
        $attributes = $this->prepareAttributes($attributes);
        $escape = $env->getFilter('escape')->getCallable();
        $escapeAttr = $this->getEscapeHtmlAttrHelper();
        $strings = [];

        foreach ($attributes as $key => $value)
        {
            $key = strtolower($key);

            if (! $value && isset($this->booleanAttributes[$key]) && ('' === $this->booleanAttributes[$key]['off']))
            {
                continue;
            }

            //check if attribute is translatable and translate it
            $value = $this->translateHtmlAttributeValue($env, $key, $value);

            // @todo Escape event attributes like AbstractHtmlElement view helper does in htmlAttribs ??
            try
            {
                $escapedAttribute = $escapeAttr($value);
                $strings[] = sprintf('%s="%s"', $escape($env, $key, 'html'), $escapedAttribute);
            }
            catch (EscaperException $x)
            {
                // If an escaper exception happens, escape only the key, and use a blank value.
                $strings[] = sprintf('%s=""', $escape($env, $key, 'html'));
            }
        }

        return implode(' ', $strings);
    }

    /**
     * Get the ID of an element.
     *
     * If no ID attribute present, attempts to use the name attribute.
     * If no name attribute is present, either, returns null.
     *
     * @return string|null
     */
    public function getId(ElementInterface $element)
    {
        $id = $element->getAttribute('id');

        if (null !== $id)
        {
            return $id;
        }

        return $element->getName();
    }

    /**
     * Adds an HTML attribute to the list of valid attributes.
     *
     * @param string $attribute
     *
     * @return AbstractHelper
     *
     * @throws InvalidArgumentException for attribute names that are invalid
     *                                  per the HTML specifications
     */
    public function addValidAttribute($attribute)
    {
        if (! $this->isValidAttributeName($attribute))
        {
            throw new InvalidArgumentException(sprintf('%s is not a valid attribute name', $attribute));
        }

        $this->validTagAttributes[$attribute] = true;

        return $this;
    }

    /**
     * Adds a prefix to the list of valid attribute prefixes.
     *
     * @param string $prefix
     *
     * @return AbstractHelper
     *
     * @throws InvalidArgumentException for attribute prefixes that are invalid
     *                                  per the HTML specifications for attribute names
     */
    public function addValidAttributePrefix($prefix)
    {
        if (! $this->isValidAttributeName($prefix))
        {
            throw new InvalidArgumentException(sprintf('%s is not a valid attribute prefix', $prefix));
        }

        $this->validTagAttributePrefixes[] = $prefix;

        return $this;
    }

    /**
     * Adds an HTML attribute to the list of translatable attributes.
     *
     * @param string $attribute
     *
     * @return AbstractHelper
     */
    public function addTranslatableAttribute($attribute)
    {
        $this->translatableAttributes[$attribute] = true;

        return $this;
    }

    /**
     * Adds an HTML attribute to the list of the default translatable attributes.
     *
     * @param string $attribute
     */
    public static function addDefaultTranslatableAttribute($attribute)
    {
        self::$defaultTranslatableHtmlAttributes[$attribute] = true;
    }

    /**
     * Adds an HTML attribute to the list of translatable attributes.
     *
     * @param string $prefix
     *
     * @return AbstractHelper
     */
    public function addTranslatableAttributePrefix($prefix)
    {
        $this->translatableAttributePrefixes[] = $prefix;

        return $this;
    }

    /**
     * Adds an HTML attribute to the list of translatable attributes.
     *
     * @param string $prefix
     */
    public static function addDefaultTranslatableAttributePrefix($prefix)
    {
        self::$defaultTranslatableHtmlAttributePrefixes[] = $prefix;
    }

    /**
     * Set translation text domain.
     *
     * @return $this
     */
    public function setTranslatorTextDomain(string $textDomain = 'default')
    {
        $this->translatorTextDomain = $textDomain;

        return $this;
    }

    /**
     * Return the translation text domain.
     */
    public function getTranslatorTextDomain(): string
    {
        return $this->translatorTextDomain;
    }

    /**
     * Retrieve the escapeHtmlAttr helper.
     *
     * @return EscapeHtmlAttr
     */
    protected function getEscapeHtmlAttrHelper()
    {
        if (! $this->escapeHtmlAttrHelper instanceof EscapeHtmlAttr)
        {
            $this->escapeHtmlAttrHelper = new EscapeHtmlAttr();
        }

        return $this->escapeHtmlAttrHelper;
    }

    /**
     * Prepare attributes for rendering.
     *
     * Ensures appropriate attributes are present (e.g., if "name" is present,
     * but no "id", sets the latter to the former).
     *
     * Removes any invalid attributes
     */
    protected function prepareAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $value)
        {
            $attribute = strtolower($key);

            if (! isset($this->validGlobalAttributes[$attribute])
                && ! isset($this->validTagAttributes[$attribute])
                && ! $this->hasAllowedPrefix($attribute)
            ) {
                unset($attributes[$key]);
                continue;
            }

            // Normalize attribute key, if needed
            if ($attribute != $key)
            {
                unset($attributes[$key]);
                $attributes[$attribute] = $value;
            }

            // Normalize boolean attribute values
            if (isset($this->booleanAttributes[$attribute]))
            {
                $attributes[$attribute] = $this->prepareBooleanAttributeValue($attribute, $value);
            }
        }

        return $attributes;
    }

    /**
     * Prepare a boolean attribute value.
     *
     * Prepares the expected representation for the boolean attribute specified.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return string
     */
    protected function prepareBooleanAttributeValue($attribute, $value)
    {
        if (! is_bool($value) && in_array($value, $this->booleanAttributes[$attribute]))
        {
            return $value;
        }

        $value = (bool) $value;

        return $value
            ? $this->booleanAttributes[$attribute]['on']
            : $this->booleanAttributes[$attribute]['off']
        ;
    }

    /**
     * Translates the value of the HTML attribute if it should be translated and this view helper has a translator.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function translateHtmlAttributeValue(Environment $env, $key, $value)
    {
        if (empty($value))
        {
            return $value;
        }

        $translator = $env->getFilter('tst');

        if (isset($this->translatableAttributes[$key]) || isset(self::$defaultTranslatableHtmlAttributes[$key]))
        {
            return $translator($value, $this->getTranslatorTextDomain());
        }
        else
        {
            foreach ($this->translatableAttributePrefixes as $prefix)
            {
                if (0 === mb_strpos($key, $prefix))
                {
                    // prefix matches => return translated $value
                    return $translator($value, $this->getTranslatorTextDomain());
                }
            }

            foreach (self::$defaultTranslatableHtmlAttributePrefixes as $prefix)
            {
                if (0 === mb_strpos($key, $prefix))
                {
                    // default prefix matches => return translated $value
                    return $translator($value, $this->getTranslatorTextDomain());
                }
            }
        }

        return $value;
    }

    /**
     * Whether the passed attribute is valid or not.
     *
     * @see https://html.spec.whatwg.org/multipage/syntax.html#attributes-2
     *     Description of valid attributes
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function isValidAttributeName($attribute)
    {
        return preg_match('/^[^\t\n\f \/>"\'=]+$/', $attribute);
    }

    /**
     * Whether the passed attribute has a valid prefix or not.
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function hasAllowedPrefix($attribute)
    {
        foreach ($this->validTagAttributePrefixes as $prefix)
        {
            if (substr($attribute, 0, strlen($prefix)) === $prefix)
            {
                return true;
            }
        }

        return false;
    }
}
