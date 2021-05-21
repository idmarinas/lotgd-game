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

namespace Lotgd\Bundle\CommentaryBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class CommentaryAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'entity.commentary.id'])
            ->add('section', null, ['label' => 'entity.commentary.section'])
            ->add('command', null, ['label' => 'entity.commentary.command'])
            ->add('comment', null, ['label' => 'entity.commentary.comment'])
            ->add('commentRaw', null, ['label' => 'entity.commentary.comment_raw'])
            ->add('postdate', null, ['label' => 'entity.commentary.postdate'])
            // ->add('extra', null, ['label' => 'entity.commentary.extra'])
            ->add('author', null, ['label' => 'entity.commentary.author'])
            ->add('authorName', null, ['label' => 'entity.commentary.author_name'])
            ->add('clan', null, ['label' => 'entity.commentary.clan_id'])
            ->add('clanRank', null, ['label' => 'entity.commentary.clan_rank'])
            ->add('clanName', null, ['label' => 'entity.commentary.clan_name'])
            ->add('clanNameShort', null, ['label' => 'entity.commentary.clan_name_short'])
            ->add('hidden', null, ['label' => 'entity.commentary.hidden'])
            ->add('hiddenComment', null, ['label' => 'entity.commentary.hidden_comment'])
            ->add('hiddenBy', null, ['label' => 'entity.commentary.hidden_by'])
            ->add('hiddenByName', null, ['label' => 'entity.commentary.hidden_by_name'])
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, ['label' => 'entity.commentary.id'])
            ->add('hidden', null, ['editable' => true, 'label' => 'entity.commentary.hidden'])
            ->add('section', null, ['label' => 'entity.commentary.section'])
            // ->add('command', null, ['label' => 'entity.commentary.command'])
            ->add('comment', null, ['label' => 'entity.commentary.comment'])
            // ->add('commentRaw', null, ['label' => 'entity.commentary.comment_raw'])
            ->add('postdate', null, ['label' => 'entity.commentary.postdate'])
            // ->add('extra', null, ['label' => 'entity.commentary.extra'])
            // ->add('author', null, ['label' => 'entity.commentary.author'])
            ->add('authorName', null, ['label' => 'entity.commentary.author_name'])
            // ->add('clan', null, ['label' => 'entity.commentary.clan_id'])
            // ->add('clanRank', null, ['label' => 'entity.commentary.clan_rank'])
            // ->add('clanName', null, ['label' => 'entity.commentary.clan_name'])
            // ->add('clanNameShort', null, ['label' => 'entity.commentary.clan_name_short'])
            // ->add('hiddenComment', null, ['label' => 'entity.commentary.hidden_comment'])
            // ->add('hiddenBy', null, ['label' => 'entity.commentary.hidden_by'])
            // ->add('hiddenByName', null, ['label' => 'entity.commentary.hidden_by_name'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            // ->add('id', null, ['label' => 'entity.commentary.id'])
            ->add('section', null, ['label' => 'entity.commentary.section'])
            ->add('command', null, ['label' => 'entity.commentary.command'])
            ->add('comment', null, ['label' => 'entity.commentary.comment'])
            ->add('commentRaw', null, ['label' => 'entity.commentary.comment_raw'])
            ->add('postdate', null, ['label' => 'entity.commentary.postdate'])
            ->add('extra', null, ['label' => 'entity.commentary.extra'])
            ->add('author', null, ['label' => 'entity.commentary.author'])
            ->add('authorName', null, ['label' => 'entity.commentary.author_name'])
            ->add('clan', null, ['label' => 'entity.commentary.clan_id'])
            ->add('clanRank', null, ['label' => 'entity.commentary.clan_rank'])
            ->add('clanName', null, ['label' => 'entity.commentary.clan_name'])
            ->add('clanNameShort', null, ['label' => 'entity.commentary.clan_name_short'])
            ->add('hidden', null, ['label' => 'entity.commentary.hidden'])
            ->add('hiddenComment', null, ['label' => 'entity.commentary.hidden_comment'])
            ->add('hiddenBy', null, ['label' => 'entity.commentary.hidden_by'])
            ->add('hiddenByName', null, ['label' => 'entity.commentary.hidden_by_name'])
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'entity.commentary.id'])
            ->add('section', null, ['label' => 'entity.commentary.section'])
            ->add('command', null, ['label' => 'entity.commentary.command'])
            ->add('comment', null, ['label' => 'entity.commentary.comment'])
            ->add('commentRaw', null, ['label' => 'entity.commentary.comment_raw'])
            ->add('postdate', null, ['label' => 'entity.commentary.postdate'])
            ->add('extra', null, ['label' => 'entity.commentary.extra'])
            ->add('author', null, ['label' => 'entity.commentary.author'])
            ->add('authorName', null, ['label' => 'entity.commentary.author_name'])
            ->add('clan', null, ['label' => 'entity.commentary.clan_id'])
            ->add('clanRank', null, ['label' => 'entity.commentary.clan_rank'])
            ->add('clanName', null, ['label' => 'entity.commentary.clan_name'])
            ->add('clanNameShort', null, ['label' => 'entity.commentary.clan_name_short'])
            ->add('hidden', null, ['label' => 'entity.commentary.hidden'])
            ->add('hiddenComment', null, ['label' => 'entity.commentary.hidden_comment'])
            ->add('hiddenBy', null, ['label' => 'entity.commentary.hidden_by'])
            ->add('hiddenByName', null, ['label' => 'entity.commentary.hidden_by_name'])
            ;
    }
}
