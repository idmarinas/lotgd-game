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

namespace Lotgd\Bundle\CoreBundle\Twig\Extension;

use Lotgd\Bundle\CoreBundle\Tool\Censor;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class GameCoreExtension extends AbstractExtension
{
    use Pattern\CoreFilter;
    use Pattern\PageGen;

    protected $request;
    protected $translator;
    protected $censor;

    public function __construct(
        RequestStack $request,
        TranslatorInterface $translator,
        Censor $censor
    ) {
        $this->request    = $request->getCurrentRequest();
        $this->translator = $translator;
        $this->censor     = $censor;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('censor', [$this->censor, 'filter']),
            new TwigFilter('yes_no', [$this, 'affirmationNegation']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('game_copyright', function (): string
            {
                return \Lotgd\Bundle\Kernel::LICENSE.\Lotgd\Bundle\Kernel::COPYRIGHT;
            }, ['is_safe' => ['html']]),
            new TwigFunction('game_page_gen', [$this, 'gamePageGen'], ['needs_environment' => true]),

            //-- Syntax highlighting of a file.
            new TwigFunction('highlight_file', function (?string $file, $return = true)
            {
                if ( ! $file)
                {
                    return '';
                }

                return \highlight_file($file, $return);
            }),
            //-- Syntax highlighting of a string.
            new TwigFunction('highlight_string', function (?string $string, $return = true)
            {
                if ( ! $string)
                {
                    return '';
                }

                return \highlight_string("<?php \n\r".$string, $return);
            }),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new TwigTest('array', function ($value)
            {
                return \is_array($value);
            }),
            new TwigTest('object', function ($value)
            {
                return \is_object($value);
            }),
            new TwigTest('instanceof', function ($instance, $class)
            {
                return $instance instanceof $class;
            }),
        ];
    }
}
