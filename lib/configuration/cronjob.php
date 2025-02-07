<?php

use Lotgd\Core\Entity\Cronjob;
use Lotgd\Core\Form\CronjobType;

$op = (string)LotgdRequest::getQuery('op');
$cronId = (int)LotgdRequest::getQuery('cronid');
$page = (int)LotgdRequest::getQuery('page', 1);

$params['cronId'] = $cronId;

$repository = Doctrine::getRepository(Cronjob::class);

if ('delcronjob' == $op) {
	$cronEntity = $repository->find($cronId);

	if ($cronEntity) {
		LotgdLog::game("`4Delete CronJob `^{$cronId}`4 by admin {$session['user']['playername']}", 'cronjob');

		LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t('flash.message.cronjob.deleted', [], $textDomain));

		LotgdKernel::get('core.cronjobs.cache')->delete('cronjobstable');

		Doctrine::remove($cronEntity);
		Doctrine::flush();
	}

	$op = '';
}

if ('' == $op) {
	$lotgdFormFactory = LotgdKernel::get('form.factory');

	$form = $lotgdFormFactory->create(CronjobType::class, [
	  'newdaycron' => LotgdSetting::getSetting('newdaycron', 0),
	], [
	  'action' => 'configuration.php?setting=cronjob&save=save',
	  'attr'   => [
		'autocomplete' => 'off',
	  ],
	]);

	$form->handleRequest(LotgdRequest::_i());

	if ($form->isSubmitted() && $form->isValid()) {
		$formIsValid = true;
		$postSettings = $form->getData();

		require_once 'lib/configuration/save.php';
	}

	$query = $repository->createQueryBuilder('u');

	$params['paginator'] = $repository->getPaginator($query, $page);
	$params['form'] = $form->createView();
} elseif ('newcronjob' == $op) {
	$params['tpl'] = 'cronjob-new';

	$lotgdFormFactory = LotgdKernel::get('form.factory');
	$entity = $repository->find($cronId);
	$entity = $entity ?: new Cronjob();

	$form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\CronjobType::class, $entity, [
	  'action' => "configuration.php?setting=cronjob&op=newcronjob&cronid={$cronId}",
	  'attr'   => [
		'autocomplete' => 'off',
	  ],
	]);

	$form->handleRequest(LotgdRequest::_i());

	$paramsFlashMessages = [];

	if ($form->isSubmitted() && $form->isValid()) {
		$entity = $form->getData();
		$message = $cronId !== 0 ? 'flash.message.cronjob.updated' : 'flash.message.cronjob.created';

		Doctrine::persist($entity);
		Doctrine::flush();

		$cronId = $entity->getId();

		LotgdLog::game(
		  '`@' . $cronId !== '' ? 'Updated' :
			'Create' . " CronJob`0 `^{$entity->getName()}`0`$ by admin {$session['user']['playername']}`0",
		  'cronjob'
		);

		LotgdKernel::get('core.cronjobs.cache')->delete('cronjobstable');

		//-- Redo form for change $cronId and set new data (generated IDs)
		$form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\CronjobType::class, $entity, [
		  'action' => "configuration.php?setting=cronjob&op=newcronjob&cronid={$cronId}",
		  'attr'   => [
			'autocomplete' => 'off',
		  ],
		]);

		LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t($message, $paramsFlashMessages, $textDomain));
	}

	Doctrine::detach($entity); //-- Avoid Doctrine save an invalid Form

	//-- In this position can updated $cronId var
	LotgdNavigation::addNavAllow("configuration.php?setting=cronjob&op=newcronjob&cronid={$cronId}");

	$params['form'] = $form->createView();
}
