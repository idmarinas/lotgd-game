<?php

require_once 'lib/template.class.php';

use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\SeparatorToDash;
use Zend\Filter\Word\UnderscoreToDash;

class LotgdTheme extends LotgdTemplate
{
    protected $twig;
    protected $themename;
    protected $themefolder;
    protected $defaultSkin;

    public function __construct(array $loader = [], array $options = [])
    {
        $this->prepareTheme();

        //-- Merge loaders
        $loader = array_merge(['themes', 'templates'], $loader);

        parent::__construct($loader, $options);
    }

    /**
     * Render a theme
     * Used in pageparts.php for render a page.
     */
    public function renderTheme($context)
    {
        return $this->render($this->getTheme(), $context);
    }

    /**
     * Renders a template of the theme.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function renderThemeTemplate($name, $context)
    {
        global $html, $session;

        $folder = $this->themefolder.'/templates';

        $userPre = $html['userPre'] ?? [];
        $user = $session['user'] ?? [];
        unset($user['password']);
        $sesion = $session ?? [];
        unset($user['user']);

        $context = array_merge(['userPre' => $userPre, 'user' => $user, 'session' => $sesion], $context);

        return $this->render("{$folder}/{$name}", $context);
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
     * Get active theme.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->themename;
    }

    /**
     * Get default skin of game.
     *
     * @return string
     */
    public function getDefaultSkin()
    {
        if (empty($this->defaultSkin))
        {
            $themename = $_COOKIE['template'] ?? '';

            if ('' == $themename || ! file_exists("themes/$themename"))
            {
                $themename = getsetting('defaultskin', 'jade.html');
            }

            if ('' == $themename || ! file_exists("themes/$themename"))
            {
                $themename = 'jade.html';
            }

            //-- Search for a valid theme in directory
            if (! file_exists("themes/$themename"))
            {
                // A generic way of allowing a theme to be selected.
                $skins = [];
                $handle = @opendir('themes');

                while (false !== ($file = @readdir($handle)))
                {
                    if (strpos($file, '.htm') > 0)
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

            $this->defaultSkin = $themename;

            savesetting('defaultskin', $themename);
        }

        if (! isset($_COOKIE['template']) || '' == $_COOKIE['template'])
        {
            $_COOKIE['template'] = $themename;
        }

        return $this->defaultSkin;
    }

    /**
     * Preparece template for use.
     *
     * @return void
     */
    private function prepareTheme()
    {
        global $y, $z, $y2, $z2, $lc, $x;

        $this->themename = $this->getDefaultSkin();

        if (empty($this->themefolder) && false === strpos($this->themefolder, $this->themename))
        {
            //-- Prepare name folder of theme, base on filename of theme
            $this->themefolder = pathinfo($this->themename, PATHINFO_FILENAME); //-- Delete extension
            $filterChain = new FilterChain();
            $filterChain
                ->attach(new StringToLower())
                ->attach(new SeparatorToDash())
                ->attach(new UnderscoreToDash())
            ;

            $this->themefolder = $filterChain->filter($this->themefolder);
        }

        //-- Seem to not have function
        // $y = 0;
        // $z = $y2^$z2;
        // $$z = $lc . $$z . '<br>';
    }
}

global $lotgd_tpl, $lotgdTpl;

$lotgdTpl = new LotgdTheme();
$lotgd_tpl = &$lotgdTpl;
