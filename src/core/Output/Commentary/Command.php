<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Output\Commentary;

use DateTime;
use Lotgd\Core\Event\Commentary as EventCommentary;

trait Command
{
    /**
     * Process commands for comentary.
     *
     * @return array
     */
    public function processCommands(array &$data): bool
    {
        //-- Special command for users
        $this->analizeSpecialCommands($data);
        //-- /game will have a specific function for the system

        //-- Find command
        $command = $data['command'] ?? null;

        //-- If commands is proccessed, process return true, not proccess addicional
        if ($command)
        {
            return $this->processCoreCommands($data);
        }

        //-- Search for custom commands
        $args = new EventCommentary(['command' => $command, 'section' => $data['section'], 'data' => &$data]);
        $this->hook->dispatch($args, EventCommentary::COMMANDS);
        $returnedHook = modulehook('commentary-command', $args->getData());

        $processed = true;

        if (isset($returnedHook['skipCommand']) && ! $returnedHook['skipCommand'])
        {
            //if for some reason you're going to involve a command that can be a mix of upper and lower case, set $args['skipCommand'] and $args['ignore'] to true and handle it in postcomment instead.
            if (isset($returnedHook['processed']) && ! $returnedHook['processed'])
            {
                $this->flashBag->add('info', $this->translator->trans('command.unrecognized', [], $this->getTranslationDomain()));
            }

            $processed = false;
        }

        return $processed;
    }

    /**
     * Analize special commands that save to data base.
     */
    public function analizeSpecialCommands(array &$data): bool
    {
        if ('/me' == substr($data['comment'], 0, 3))
        {
            $data['comment'] = trim(substr($data['comment'], 3));
            $data['command'] = 'me';
        }
        elseif (':grem' == substr($data['comment'], 0, 5) || '/grem' == substr($data['comment'], 0, 5))
        {
            $data['comment'] = trim(substr($data['comment'], 5));
            $data['command'] = 'grem';
        }
        elseif ('::grem' == substr($data['comment'], 0, 6))
        {
            $data['comment'] = trim(substr($data['comment'], 6));
            $data['command'] = 'grem';
        }
        elseif ('::' == substr($data['comment'], 0, 2))
        {
            $data['comment'] = trim(substr($data['comment'], 2));
            $data['command'] = 'me';
        }
        elseif (':' == substr($data['comment'], 0, 1))
        {
            $data['comment'] = trim(substr($data['comment'], 1));
            $data['command'] = 'me';
        }

        //-- If process special commands return
        return (bool) ($data['command'] ?? false);
    }

    /**
     * @return bool Returns whether the command was processed.
     */
    protected function processCoreCommands(array &$data): bool
    {
        $command = ucfirst($data['command']);
        $command = "processCommand{$command}";

        if (method_exists($this, $command))
        {
            return $this->{$command}($data);
        }

        return true;
    }

    /**
     * Command GREEM: Deletes the user's last written comment, only if no more than 3 minutes have passed.
     */
    private function processCommandGrem(): bool
    {
        global $session;

        $query = $this->getRepository()->createQueryBuilder('u');

        /** @var \Lotgd\Core\Entity\Commentary $last */
        $last = $query
            ->where('u.author = :acct AND u.command <> :grem AND u.command <> :trans')

            ->orderBy('u.postdate', 'DESC')

            ->setParameter('acct', $session['user']['acctid'])
            ->setParameter('grem', 'grem')
            ->setParameter('trans', 'trans')

            ->setMaxResults(1)

            ->getQuery()
            ->getOneOrNullResult()
        ;

        //-- Find a comment
        if ($last)
        {
            $post = $last->getPostdate();
            $diff = $post->diff(new DateTime('now'));

            //-- Delete only if no more than 3 minutes have passed
            if ($diff->y || $diff->m || $diff->d || $diff->h || $diff->i > 2)
            {
                $this->flashBag->add('info', $this->translator->trans('command.grem.old', [], $this->getTranslationDomain()));

                return false;
            }

            $extra = $last->getExtra();
            $extra = \is_array($extra) ? $extra : [];

            $last
                ->setCommand('grem')
                ->setComment('command.grem.delete')
                ->setTranslatable(true)
                ->setExtra(array_merge($extra, ['translation_domain' => $this->getTranslationDomain()]))
            ;

            $this->doctrine->persist($last);
            $this->doctrine->flush();
        }

        return false;
    }
}
