<?php

/**
 * This file is part of "LoTGD Core Package".
 *
 * @see     https://github.com/idmarinas/lotgd-game
 *
 * @license LICENSE.txt
 * @author  IDMarinas
 *
 * @since   7.2.0
 */

namespace Lotgd\Core\Controller\Stimulus;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Controller\LotgdControllerInterface;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Output\Format;
use Lotgd\Core\Pattern\LotgdControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BansController extends AbstractController implements LotgdControllerInterface
{
	use LotgdControllerTrait;

	private EntityManagerInterface $em;
	private Format                 $format;

	public function __construct (EntityManagerInterface $em, Format $format)
	{
		$this->em = $em;
		$this->format = $format;
	}

	public function index (Request $request): Response
	{
		$content = '';

		$id = $request->request->get('id');
		$ip = $request->request->get('ip');

		try {
			$query = $this->em->createQuery(
				"SELECT c.name FROM Lotgd\Core\Entity\Bans b, LotgdCore:User a
            LEFT JOIN LotgdCore:Avatar c WITH c.acct = a.acctid
            WHERE
                (b.ipfilter = :ip AND b.uniqueid = :id) AND
                ( (substring(a.lastip,1,length(b.ipfilter)) = b.ipfilter AND b.ipfilter != '') OR (a.uniqueid = b.uniqueid AND b.uniqueid != '') )
            "
			);

			$query
				->setParameter('id', $id)
				->setParameter('ip', $ip)
			;

			$result = $query->execute();
		} catch (Throwable $exception) {
			$result = [];
		}

		foreach ($result as $acct) {
			$content .= $this->format->colorize($acct['name']) . '<br>';
		}

		return $this->renderBlock('components/modal.html.twig', 'dialog', [
			'content'      => $content ?: '---',
			'close_button' => true,
		]);
	}

	public function allowAnonymous (): bool
	{
		return false;
	}

	public function overrideForcedNav (): bool
	{
		return true;
	}
}