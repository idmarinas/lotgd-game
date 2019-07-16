<?php

// translator ready
// addnews ready
// mail ready

/**
 *Documentated by Catscradler
 *Add to the user's log
 *if $field $value and $consolidate have values, entry will be merged with existing line from today with identical $field.
 *otherwise, a new line will be added to the log.
 *
 *@param string $message the text to be added
 *@param int $target acctid of the user on the receiving end of the event eg. the user who did NOT initiate PvP, gold transfer recipient (optional)
 *@param int $user acctid of the user the log entry is about (optional, defaults to current user)
 *@param string $field the label for this line, appears as first word on this line in the log eg. healing, forestwin (optional)
 *@param int $value how much was gained or lost.  Only useful if also using $field and $consolidate (optional)
 *@param bool $consolidate add $value to previous log lines with the same $field, keeping a running total for today (optional, defaults to true)
 */
function debuglog($message, $target = false, $user = false, $field = false, $value = false, $consolidate = true)
{
    global $session;

    $repository = \Doctrine::getRepository('LotgdCore:Debuglog');

    if (false === $target)
    {
        $target = 0;
    }

    if (false === $user)
    {
        $user = $session['user']['acctid'];
    }

    $corevalue = $value;

    $id = 0;

    if (false !== $field && false !== $value && $consolidate)
    {
        $query = $repository->createQueryBuilder('u');
        $result = $query
            ->where('u.actor = :user AND u.field = :field AND u.date > :date')

            ->setParameter('user', $user)
            ->setParameter('field', $field)
            ->setParameter('date', new \DateTime(date('Y-m-d 00:00:00')))

            ->getQuery()
            ->getSingleArrayResult()
        ;

        if (count($result))
        {
            $value = $result['value'] + $value;
            $message = $result['message'];
            $id = $result['id'];
        }
    }

    if (false !== $corevalue)
    {
        $message .= " ($corevalue)";
    }

    if (false === $field)
    {
        $field = '';
    }

    if (false === $value)
    {
        $value = 0;
    }

    $entity = $repository->find($id);
    $entity = $repository->hydrateEntity([
        'date' => new \DateTime('now'),
        'actor' => $user,
        'target' => $target,
        'message' => $message,
        'field' => $field,
        'value' => $value
    ], $entity);

    \Doctrine::persist($entity);
    \Doctrine::flush();
}
