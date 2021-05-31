<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Twig\Extension;

use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadScript;
use Laminas\View\Helper\HeadStyle;
use Laminas\View\Helper\HeadTitle;
use Laminas\View\Helper\InlineScript;
use Laminas\View\Helper\Placeholder\Container\AbstractContainer as Placeholder;
use Lotgd\Core\Http\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class for use Laminas Helpers: like show/capture head/inline scripts/styles and files.
 * and others.
 */
class Helpers extends AbstractExtension
{
    protected $request;
    protected $headLink;
    protected $headMeta;
    protected $headScript;
    protected $headStyle;
    protected $headTitle;
    protected $inlineScript;
    protected $basePath;

    public function __construct(
        Request $request,
        HeadLink $headLink,
        HeadMeta $headMeta,
        HeadScript $headScript,
        HeadStyle $headStyle,
        HeadTitle $headTitle,
        InlineScript $inlineScript
    ) {
        $this->request      = $request;
        $this->headLink     = $headLink;
        $this->headMeta     = $headMeta;
        $this->headScript   = $headScript;
        $this->headStyle    = $headStyle;
        $this->headTitle    = $headTitle;
        $this->inlineScript = $inlineScript;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('head_link', [$this, 'headLink']),
            new TwigFunction('head_meta', [$this, 'headMeta']),
            new TwigFunction('head_script', [$this, 'headScript']),
            new TwigFunction('head_style', [$this, 'headStyle']),
            new TwigFunction('head_title', [$this, 'headTitle']),
            new TwigFunction('inline_script', [$this, 'inlineScript']),
            new TwigFunction('base_path', [$this, 'basePath']),
        ];
    }

    /**
     * View Helper Method.
     *
     * @param array  $attributes
     * @param string $placement
     *
     * @return HeadLink
     */
    public function headLink(?array $attributes = null, $placement = Placeholder::APPEND)
    {
        return $this->headLink->__invoke($attributes, $placement);
    }

    /**
     * Retrieve object instance; optionally add meta tag.
     *
     * @param string $content
     * @param string $keyValue
     * @param string $keyType
     * @param array  $modifiers
     * @param string $placement
     *
     * @return HeadMeta
     */
    public function headMeta($content = null, $keyValue = null, $keyType = 'name', $modifiers = [], $placement = Placeholder::APPEND)
    {
        return $this->headMeta->__invoke($content, $keyValue, $keyType, $modifiers, $placement);
    }

    /**
     * Return headScript object.
     *
     * @param string $mode      Script or file
     * @param string $spec      Script/url
     * @param string $placement Append, prepend, or set
     * @param array  $attrs     Array of script attributes
     * @param string $type      Script type and/or array of script attributes
     *
     * @return HeadScript
     */
    public function headScript($mode = HeadScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = [], $type = 'text/javascript')
    {
        return $this->headScript->__invoke($mode, $spec, $placement, $attrs, $type);
    }

    /**
     * Return headStyle object.
     *
     * @param string       $content    Stylesheet contents
     * @param string       $placement  Append, prepend, or set
     * @param string|array $attributes Optional attributes to utilize
     *
     * @return HeadStyle
     */
    public function headStyle($content = null, $placement = 'APPEND', $attributes = [])
    {
        return $this->headStyle->__invoke($content, $placement, $attributes);
    }

    /**
     * Retrieve placeholder for title element and optionally set state.
     *
     * @param string $title
     * @param string $setType
     *
     * @return HeadTitle
     */
    public function headTitle($title = null, $setType = null)
    {
        return $this->headTitle->__invoke($title, $setType);
    }

    /**
     * Return InlineScript object.
     *
     * @param string $mode      Script or file
     * @param string $spec      Script/url
     * @param string $placement Append, prepend, or set
     * @param array  $attrs     Array of script attributes
     * @param string $type      Script type and/or array of script attributes
     *
     * @return InlineScript
     */
    public function inlineScript($mode = InlineScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = [], $type = 'text/javascript')
    {
        return $this->inlineScript->__invoke($mode, $spec, $placement, $attrs, $type);
    }

    /**
     * Returns site's base path, or file with base path prepended.
     *
     * $file is appended to the base path for simplicity.
     *
     * @return string
     */
    public function basePath(?string $file = null)
    {
        if ( ! $this->basePath)
        {
            $this->basePath = $this->request->getBasePath();
        }

        if (null !== $file)
        {
            $file = '/'.\ltrim($file, '/');
        }

        return $this->basePath.$file;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'head';
    }
}
