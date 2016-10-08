<?php

use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\SeparatorToDash;
use Zend\Filter\Word\UnderscoreToDash;

class LotgdTemplate
{
	protected $twig;
	protected $templatename;
	protected $themefolder;

	public function __construct()
	{
		$this->prepareTheme();

		$loader = new Twig_Loader_Filesystem(['themes', 'templates']);
		$this->twig = new Twig_Environment($loader, [
			'cache' => 'cache/templates',
			'autoescape' => false
		]);

		//-- Add filters to Twig
		foreach($this->getFilters() as $filter)
		{
			$this->twig->addFilter($filter);
		}
	}

	/**
	 *
	 */
	public function renderTheme($context)
	{
		return $this->twig->render($this->getTheme(), $context);
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
		return $this->twig->render($folder.'/'.$name, $context);
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
		return $this->twig->render($name, $context);
	}

	/**
	 * Filters create for LOTGD
	 *
	 * @return array
	 */
	private function getFilters()
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
		return $this->templatename;
	}

	/**
	 * Preparece template for use
	 *
	 * @return array
	 */
	private function prepareTheme()
	{
		global $templatename, $session, $y, $z, $y2, $z2, $lc, $x, $_defaultskin;

		if ('' != $_COOKIE['template']) $templatename = $_COOKIE['template'];
		if ('' == $templatename || ! file_exists("themes/$templatename")) $templatename = getsetting('defaultskin', $_defaultskin);
		if ('' == $templatename || ! file_exists("themes/$templatename")) $templatename = $_defaultskin;

		$this->templatename = $templatename;

		//-- Prepare name folder of theme, base on filename of theme
		$this->themefolder = pathinfo($this->templatename, PATHINFO_FILENAME);//-- Delete extension
		$filterChain = new FilterChain();
		$filterChain
			->attach(new StringToLower())
			->attach(new SeparatorToDash())
			->attach(new UnderscoreToDash());

		$this->themefolder = $filterChain->filter($this->themefolder);

		$y = 0;
		$z = $y2^$z2;
		$$z = $lc . $$z . '<br>';
	}
}

function templatereplace()
{
	return;
}

$lotgd_tpl = new LotgdTemplate;