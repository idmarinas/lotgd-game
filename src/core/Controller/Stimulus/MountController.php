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

use Lotgd\Core\Controller\LotgdControllerInterface;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Output\Format;
use Lotgd\Core\Pattern\LotgdControllerTrait;
use Lotgd\Core\Repository\AvatarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MountController extends AbstractController implements LotgdControllerInterface
{
	use LotgdControllerTrait;

	private AvatarRepository $repository;

	private Format           $format;

	public function __construct (AvatarRepository $repository, Format $format)
	{
		$this->repository = $repository;
		$this->format = $format;
	}

	public function index (Request $request): Response
	{
		$mountId = $request->query->getInt('id');
		$content = '';

		$result = $this->repository->findBy(['hashorse' => $mountId], null, 50);

		foreach ($result as $item) {
			$content .= $this->format->colorize($item->getName()) . '<br />';
		}

		if (count($result) > 50) {
			$content .= '... <br />';
		}

		return $this->renderBlock('components/modal.html.twig', 'modal_inner_container', [
		  'content'      => $content ?: '---',
		  'close_button' => true,
		  'controller'   => 'remote-modal',
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
