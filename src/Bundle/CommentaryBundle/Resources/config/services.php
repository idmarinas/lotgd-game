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

use Lotgd\Bundle\CommentaryBundle\Admin\CommentaryAdmin;
use Lotgd\Bundle\CommentaryBundle\Entity\Commentary;
use Lotgd\Bundle\CommentaryBundle\Service\Commentary as ServiceCommentary;
use Lotgd\Bundle\CommentaryBundle\Block\CommentaryBlock;
use Lotgd\Bundle\CommentaryBundle\EventSubscriber\CommentarySubscriber;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\CommentaryBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdCommentaryBundle.php',
            ])
        ->load('Lotgd\Bundle\CommentaryBundle\Controller\\', '../../Controller/')
            ->tag('controller.service_arguments')


        //-- Admin for commentary
        // ->set('lotgd_commentary.admin', CommentaryAdmin::class)
        // ->args([null, Commentary::class, null])
        // ->call('setTranslationDomain', ['lotgd_commentary_admin'])
        // ->tag('sonata.admin', [
        //     'manager_type' => 'orm',
        //     'group' => 'menu.admin.action.group',
        //     'label' => 'menu.admin.commentary.label_commentary',
        //     'label_catalogue' => 'lotgd_commentary_admin',
        //     'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        // ])
        // ->public()

        //-- Commentary Block
        ->set('lotgd.commentary.template.block.commentary', CommentaryBlock::class)
            ->args([new ReferenceConfigurator('twig')])
            ->call('setThread', [new ReferenceConfigurator('fos_comment.manager.thread')])
            ->call('setComment', [new ReferenceConfigurator('fos_comment.manager.comment')])
            ->call('setRequest', [new ReferenceConfigurator('request_stack')])
            ->tag('sonata.block')

        //-- Subscribers
        ->set(CommentarySubscriber::class)
            ->tag('kernel.event_subscriber')
    ;
};
