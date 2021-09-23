<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Navigation;

use Laminas\Stdlib\ArrayUtils;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class for construct a navigation menu in LotGD.
 *
 * This version avoid adding repeated links.
 */
class Navigation
{
    use Pattern\CustomClass;
    use Pattern\Links;
    use Pattern\Menu;
    use Pattern\Pagination;

    /**
     * Default text domain for navigation menu.
     */
    public const DEFAULT_NAVIGATION_TEXT_DOMAIN = 'navigation_app';

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
    protected $lastHeader = '';

    /**
     * Text domain for translator.
     *
     * @var string
     */
    protected $textDomain = self::DEFAULT_NAVIGATION_TEXT_DOMAIN;

    /**
     * Previous text domain.
     *
     * @var string
     */
    protected $textDomainPrev = '';

    private $dispatcher;
    private $request;
    private $settings;

    public function __construct(EventDispatcherInterface $dispatcher, Request $request, Settings $settings)
    {
        $this->dispatcher = $dispatcher;
        $this->request    = $request;
        $this->settings   = $settings;
    }

    /**
     * Added nav header in navigation menu.
     *
     * @param string $header  Name for a header
     * @param array  $options Is a options for header.
     *                        Options can have a multiple values like:
     *                        [
     *                           //-- Core use this `translate` and `params` if exist
     *                           'translate' => true, //-- Determine if want translate header or not
     *                           'textDomain' => 'navigation_app', //-- Determine domain for a nav.
     *                           'params' => [ //-- This is used en translation, if you need
     *                               'key' => 'value',
     *                               'key1' => 'value1',
     *                               'key2' => 'value2',
     *                           ],
     *                           //-- Can add other data if you need
     *                        ]
     *
     * @return $this
     */
    public function addHeader(string $header, array $options = [])
    {
        if ( ! isset($this->navigation[$header]))
        {
            $this->navigation[$header] = [];
        }

        $this->headers[$header] = ArrayUtils::merge([
            'translate'  => true,
            'textDomain' => $this->getTextDomain(),
            'hiddeEmpty' => true,
            'attributes' => [
                'class' => $this->getClassHeader(),
            ],
        ], $options);
        $this->lastHeader = $header;

        return $this;
    }

    /**
     * Add header but not translate.
     */
    public function addHeaderNotl(string $header, array $options = [])
    {
        return $this->addHeader($header, ArrayUtils::merge([
            'translate'  => false,
            'hiddeEmpty' => true,
            'attributes' => [
                'class' => $this->getClassHeader(),
            ],
        ], $options));
    }

    /**
     * Add nav to navigation menu.
     */
    public function addNav(?string $label, ?string $link = null, array $options = [])
    {
        return $this->addItem($label, $link, ArrayUtils::merge([
            'translate'  => true,
            'textDomain' => $this->getTextDomain(),
            'attributes' => [
                'class' => $this->getClassNav(),
            ],
        ], $options));
    }

    /**
     * Add an external link to navigation menu.
     * Use this for add a extra security (rel="noopener noreferrer") and open in new tab.
     */
    public function addNavExternal(?string $label, ?string $link = null, array $options = [])
    {
        return $this->addItem($label, $link, ArrayUtils::merge([
            'translate'      => true,
            'extraParamLink' => false,
            'textDomain'     => $this->getTextDomain(),
            'attributes'     => [
                'class'  => $this->getClassNav(),
                'target' => '_blank',
                'rel'    => 'noopener noreferrer',
            ],
        ], $options));
    }

    /**
     * Add a nav to navigation menu but not translate.
     */
    public function addNavNotl(?string $label, ?string $link = null, array $options = [])
    {
        return $this->addItem($label, $link, ArrayUtils::merge([
            'translate'  => false,
            'attributes' => [
                'class' => $this->getClassNav(),
            ],
        ], $options));
    }

    /**
     * Add a allowed nav for user.
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
        $this->textDomain     = $domain;

        return $this;
    }

    /**
     * Get text domain for translator.
     */
    public function getTextDomain(): string
    {
        return $this->textDomain;
    }

    /**
     * Get the last header created.
     */
    public function getLastHeader(): string
    {
        if ( $this->lastHeader === '' || $this->lastHeader === '0')
        {
            $this->addHeader('common.category.navigation', ['textDomain' => self::DEFAULT_NAVIGATION_TEXT_DOMAIN]);
        }

        return $this->lastHeader;
    }

    /**
     * Get structure of navigation.
     */
    public function getNavigation()
    {
        bdump($this->navigation, 'Navigation menu');

        return $this->navigation;
    }

    /**
     * Get information of headers.
     */
    public function getHeaders()
    {
        bdump($this->headers, 'Info of headers');

        return $this->headers;
    }

    /**
     * Get information of navs.
     */
    public function getNavs()
    {
        bdump($this->navs, 'Info of navs');

        return $this->navs;
    }

    /**
     * Determines if there are any navs for the player.
     */
    public function checkNavs(): bool
    {
        foreach ($this->navs as $navs)
        {
            if ( ! \is_array($navs) || ! empty($navs))
            {
                continue;
            }

            foreach ($navs as $nav)
            {
                //-- If have 1 allowed nav, return true
                if ( ! $this->isBlocked($nav['link']))
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get default text domain.
     */
    public function getDefaultTextDomain(): string
    {
        return self::DEFAULT_NAVIGATION_TEXT_DOMAIN;
    }

    /**
     * Private function to add a nav in navigation menu.
     *
     * @return $this
     */
    protected function addItem(?string $label, ?string $link = null, array $options = [])
    {
        global $session;

        //-- Add nav header if not have link.
        //-- It's for compatibility, the best way to add a header is with the addHeader function
        if ( ! $link && $label)
        {
            //-- Is better use addHeader function for add header.
            return $this->addHeader($label, ArrayUtils::merge(
                $options,
                [
                    'attributes' => [ //-- To prevent a header from having the class of a navigation menu.
                        'class' => $this->getClassHeader(), //-- This will overwrite the custom class.
                    ],
                ]
            ));
        }

        $extra = '';

        if ($options['extraParamLink'] ?? true)
        {
            //-- Add link to allowed navs
            $extra = $this->getExtraParamLink($link);

            if (false !== ($pos = strpos($link, '#')))
            {
                $sublink                                         = substr($link, 0, $pos);
                $session['user']['allowednavs'][$sublink]        = true;
                $session['user']['allowednavs'][$sublink.$extra] = true;
            }

            $session['user']['allowednavs'][$link]        = true;
            $session['user']['allowednavs'][$link.$extra] = true;

            $this->addLink($link);
            $this->addLink($link.$extra);
        }

        //-- There is no label to add to the menu
        if ( ! $label)
        {
            return $this;
        }

        $key = $this->getKeyForNav($this->navigation[$this->getLastHeader()] ?? [], $options['remplace'] ?? false, $label);

        $this->navigation[$this->getLastHeader()][$key] = $label;
        $this->navs[$this->getLastHeader()][$key]       = ArrayUtils::merge($options, [
            'link'       => $link,
            'attributes' => [
                'href' => $link.$extra,
            ],
        ]);

        return $this;
    }

    /**
     * Get key for nav.
     */
    protected function getKeyForNav(array $navs, bool $remplace, string $label): int
    {
        if ( ! $remplace)
        {
            return \count($navs);
        }

        $key = array_search($label, $navs);

        if (false === $key)
        {
            $key = \count($navs);
        }

        return $key;
    }

    /**
     * Get a extra param for link.
     */
    protected function getExtraParamLink(?string $link): string
    {
        global $session;

        if ( ! $link)
        {
            return '';
        }

        //-- Replace first & for ?
        if (false === strpos($link, '?') && false !== strpos($link, '&'))
        {
            $link = preg_replace('/[&]/', '?', $link, 1);
        }

        return sprintf(
            '%sc=%s',
            (false === strpos($link, '?') ? '?' : '&'),
            $session['counter'] ?? 0 // Avoid error of undefined index, in /jaxon.php and in /login.php?op=logout (Don't know why)
        );
    }
}
