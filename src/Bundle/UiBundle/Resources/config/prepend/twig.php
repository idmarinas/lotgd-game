<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void
{
    $container->extension('twig', [
        'form_themes' => [
            '@LotgdUi/form/semantic-ui-form-theme.html.twig',
            '@SonataForm/Form/datepicker.html.twig',
        ],
    ]);
};
