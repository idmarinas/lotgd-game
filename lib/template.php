<?php

use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\SeparatorToDash;
use Zend\Filter\Word\UnderscoreToDash;

class LotgdTemplate extends Twig_Environment
{
	protected $twig;
	protected $themename;
	protected $themefolder;
	protected $defaultSkin;

	public function __construct(array $loader = [], array $options = [])
	{
		$this->prepareTheme();

        //-- Merge options
		$default = [
			'cache' => 'cache/templates',
			'autoescape' => false
        ];
        $options = array_merge($default, $options);

        //-- Merge loaders
        $loader = array_merge(['themes', 'templates'], $loader);
		$loader = new Twig_Loader_Filesystem($loader);

		parent::__construct($loader, $options);

		//-- Add filters to Twig
		foreach($this->lotgdFilters() as $filter)
		{
			$this->addFilter($filter);
		}

		//-- Add functions to Twig
		foreach($this->lotgdFunctions() as $function)
		{
			$this->addFunction($function);
		}
	}

	/**
	 * Render a theme
     * Used in pageparts.php for render a page
	 */
	public function renderTheme($context)
	{
		return $this->render($this->getTheme(), $context);
	}

	/**
	 * Renders a template of the theme
	 *
	 * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
	 */
	public function renderThemeTemplate($name, $context)
	{
		$folder = $this->themefolder . '/templates';
		return $this->render($folder.'/'.$name, $context);
	}

	/**
	 * Renders a template of LOTGD.
	 *
	 * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
	 */
	public function renderLotgdTemplate($name, $context)
	{
		return $this->render($name, $context);
	}

	/**
	 * Filters create for LOTGD
	 *
	 * @return array
	 */
	private function lotgdFilters()
	{
		return [
			//-- Access to appoencode function in template
			new Twig_SimpleFilter('appoencode', function ($string)
			{
				return appoencode($string, true);
			}),
			//-- Access to color_sanitize function in template
			new Twig_SimpleFilter('color_sanitize', function ($string)
			{
				return color_sanitize($string);
			}),
			//-- Add a link, but not nav
			new Twig_SimpleFilter('lotgd_url', function ($url)
			{
				addnav('', $url);

				return $url;
			}),
			//-- Translate a text in template
			new Twig_SimpleFilter('t', function ($data, $namespace = false)
			{
                if (is_array($data))
                {
                    $text = str_replace('`%','`%%', $data[0]);
                    unset($data[0]);

                    return vsprintf(translate_inline($text, $namespace), $data);
                }
                else return translate_inline($data, $namespace);
			})
		];
	}

    private function lotgdFunctions()
    {
        return [
            new Twig_SimpleFunction('modulehook', function ($name, $data) {
                return modulehook($name, $data);
            })
        ];
    }

	/**
	 * Get active theme
	 *
	 * @return string
	 */
	public function getTheme()
	{
		return $this->themename;
	}

	/**
	 * Preparece template for use
	 *
	 * @return array
	 */
	private function prepareTheme()
	{
		global $y, $z, $y2, $z2, $lc, $x;

		$this->themename = $this->getDefaultSkin();

        if (empty($this->themefolder) && false === strpos($this->themefolder, $this->themename))
        {
            //-- Prepare name folder of theme, base on filename of theme
            $this->themefolder = pathinfo($this->themename, PATHINFO_FILENAME);//-- Delete extension
            $filterChain = new FilterChain();
            $filterChain
                ->attach(new StringToLower())
                ->attach(new SeparatorToDash())
                ->attach(new UnderscoreToDash());

            $this->themefolder = $filterChain->filter($this->themefolder);
        }

		//-- Seem to not have function
		// $y = 0;
		// $z = $y2^$z2;
		// $$z = $lc . $$z . '<br>';
	}

	/**
	 * Get default skin of game
	 *
	 * @return string
	 */
	public function getDefaultSkin()
	{
        if (empty($this->defaultSkin))
        {
            $themename = '';

            if (isset($_COOKIE['template']) && '' != $_COOKIE['template']) $themename = $_COOKIE['template'];
            if ('' == $themename || ! file_exists("themes/$themename")) $themename = getsetting('defaultskin', 'jade.html');
            if ('' == $themename || ! file_exists("themes/$themename")) $themename = 'jade.html';

            //-- Search for a valid theme in directory
            if (! file_exists("themes/$themename"))
            {
                // A generic way of allowing a theme to be selected.
                $skins = [];
                $handle = @opendir('themes');
                while (false !== ($file = @readdir($handle)))
                {
                    if (strpos($file,'.htm') > 0)
                    {
                        $skins[] = $file;

                        break; //-- We have 1 theme, no need more
                    }
                }

                if (count($skins))
                {
                    $themename = $skins[0];
                }
            }

            if (! isset($_COOKIE['template']) || $_COOKIE['template'] == '') $_COOKIE['template'] = $themename;

            $this->defaultSkin = $themename;

            savesetting('defaultskin', $themename);
        }

		return $this->defaultSkin;
	}
}

function templatereplace()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 2.0.0; and delete in version 3.0.0.',
        __METHOD__
    ), E_USER_DEPRECATED);

	return;
}

$lotgd_tpl = new LotgdTemplate;
