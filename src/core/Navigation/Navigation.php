<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Navigation;

/**
 * Class for construct a navigation menu in LotGD.
 *
 * This version avoid adding repeated links.
 */
class Navigation
{
    use Pattern\Links;

    /**
     * Headers for navigation menu with options.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Navs for navigation menu with options.
     *
     * @var array
     */
    protected $navs = [];

    /**
     * Navigation menu.
     *
     * @var array
     */
    protected $navigation = [];

    /**
     * Last header created.
     *
     * @var string
     */
    protected $lastHeader;

    /**
     * Text domain for translator.
     *
     * @var string
     */
    protected $textDomain = 'navigation-app';

    /**
     * Previous text domain.
     *
     * @var string
     */
    protected $textDomainPrev = '';

    /**
     * Added nav header in navigation menu.
     *
     * @param string $header  Name for a header
     * @param array  $options Is a options for header.
     *                        Options can have a multiple values like:
     *                        [
     *                           //-- Core use this `translate` and `params` if exist
     *                           'translate' => true, //-- Determine if want translate header or not
     *                           'textDomain' => 'navigation-app', //-- Determine domain for a nav.
     *                               'params' => [ //-- This is used en translation, if you need
     *                                   'key' => 'value',
     *                                   'key1' => 'value1',
     *                                   'key2' => 'value2',
     *                               ],
     *                           //-- Can add other data if you need
     *                        ]
     *
     * @return $this
     */
    public function addHeader(string $header, array $options = [])
    {
        if (! isset($this->navigation[$header]))
        {
            $this->navigation[$header] = [];
        }

        $this->headers[$header] = \array_merge_recursive([
            'translate' => true,
            'textDomain' => $this->getTextDomain(),
            'attributes' => [
                'class' => 'navhead'
            ]
        ], $options);
        $this->lastHeader = $header;

        return $this;
    }

    /**
     * Add header but not translate.
     *
     * @param string $header
     * @param array  $options
     * @return void
     */
    public function addHeaderNotl(string $header, array $options = [])
    {
        return $this->addHeader($header, \array_merge_recursive([
            'translate' => false,
            'attributes' => [
                'class' => 'navhead'
            ]
        ], $options));
    }

    /**
     * Add nav to navigation menu.
     *
     * @param string|null $label
     * @param string|null $link
     * @param array       $options
     */
    public function addNav(?string $label, ?string $link = null, array $options = [])
    {
        return $this->addItem($label, $link, \array_merge_recursive([
            'translate' => true,
            'textDomain' => $this->getTextDomain(),
            'attributes' => [
                'class' => 'nav'
            ]
        ], $options));
    }

    /**
     * Add a nav to navigation menu but not translate.
     *
     * @param string|null $label
     * @param string|null $link
     * @param array       $options
     */
    public function addNavNotl(?string $label, ?string $link = null, array $options = [])
    {
        return $this->addItem($label, $link, \array_merge_recursive([
            'translate' => false,
            'attributes' => [
                'class' => 'nav'
            ]
        ], $options));
    }

    /**
     * Add a allowed nav for user.
     *
     * @param string $link
     *
     * @return $this
     */
    public function addNavAllow(string $link)
    {
        return $this->addItem(null, $link);
    }

    /**
     * Set text domain for translator.
     *
     * @param string|null $domain With null reset to previous text domain
     *
     * @return $this
     */
    public function setTextDomain(?string $domain = null)
    {
        //-- Reset to previous text domain
        if (null === $domain || '' == $domain)
        {
            $this->textDomain = $this->textDomainPrev;

            return $this;
        }

        $this->textDomainPrev = $this->getTextDomain();
        $this->textDomain = $domain;

        return $this;
    }

    /**
     * Get text domain for translator.
     *
     * @return string
     */
    public function getTextDomain(): string
    {
        return $this->textDomain;
    }

    /**
     * Get the last header created.
     *
     * @return string
     */
    public function getLastHeader(): string
    {
        if (! $this->lastHeader)
        {
            $this->addHeader('common.category.navigation');
        }

        return $this->lastHeader;
    }

    /**
     * Undocumented function.
     */
    public function getNavigation()
    {
        bdump($this->navigation, 'Navigation menu');

        return $this->navigation;
    }

    /**
     * Undocumented function.
     */
    public function getHeaders()
    {
        bdump($this->headers, 'Info of headers');

        return $this->headers;
    }

    /**
     * Undocumented function.
     */
    public function getNavs()
    {
        bdump($this->navs, 'Info of navs');

        return $this->navs;
    }

    /**
     * Private function to add a nav in navigation menu.
     *
     * @param string|null $label
     * @param string|null $link
     * @param array       $options
     *
     * @return $this
     */
    protected function addItem(?string $label, ?string $link = null, array $options = [])
    {
        //-- Add nav header if not have link.
        if (! $link && $label)
        {
            return $this->addHeader($label, $options);
        }

        //-- Add link to allowed navs
        $extra = $this->getExtraParamLink($link);

        if (false !== ($pos = strpos($link, '#')))
        {
            $sublink = substr($link, 0, $pos);
            $session['user']['allowednavs'][rawurlencode($sublink.$extra)] = true;
        }

        $session['user']['allowednavs'][rawurlencode($link.$extra)] = true;

        $this->addLink($link);
        $this->addLink($link.$extra);

        //-- There is no label to add to the menu
        if (! $label)
        {
            return $this;
        }

        $key = count($this->navigation[$this->getLastHeader()] ?? []);

        $this->navigation[$this->getLastHeader()][$key] = $label;
        $this->navs[$this->getLastHeader()][$key] = array_merge_recursive($options, [
            'link' => $link,
            'attributes' => [
                'href' => $link.$extra
            ]
        ]);

        return $this;
    }

    /**
     * Get a extra param for link.
     *
     * @param string|null $link
     *
     * @return string
     */
    protected function getExtraParamLink(?string $link): string
    {
        global $session;

        if (! $link)
        {
            return '';
        }

        return sprintf('%sc=%s',
            (false === strpos($link, '?') ? '?' : '&'),
            $session['counter']
        );
    }
}
