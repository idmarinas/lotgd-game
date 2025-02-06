<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see     https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author  IDMarinas
 *
 * @since   5.5.0
 */

namespace Lotgd\Core\Service;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaypalButtons implements EventSubscriberInterface
{
	/**
	 * @return array<string, mixed>
	 */
	public static function getSubscribedEvents (): array
	{
		return ['sonata.block.event.lotgd_core.paypal' => 'onBlock'];
	}

	public function onBlock (BlockEvent $event)
	{
		$block = new Block();
		$block->setId(uniqid('', true)); // set a fake id
		$block->setSettings($event->getSettings());
		$block->setType('lotgd.core.template.block.donation.buttons');

		$event->addBlock($block);
	}
}
