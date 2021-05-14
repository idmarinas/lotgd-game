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

use Lotgd\Bundle\CommentaryBundle\Entity;

return static function (ContainerConfigurator $container): void {
    $container->extension('fos_comment', [
        'db_driver' => 'orm',
        'class' => [
            'model' => [
                'comment' => Entity\Comment::class,
                'thread' => Entity\Thread::class
            ]
        ]
    ]);
};
