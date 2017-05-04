<?php

use Zend\Cache\Storage\Adapter\Filesystem;

use Zend\Cache\Storage\Plugin\PluginOptions;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\Plugin\ExceptionHandler;

class LotgdCache extends Filesystem
{
	public function __construct(array $options = [])
	{
		if (empty($options))
		{
			$options = [
				'namespace' => 'lotgd',
				'cache_dir' => 'cache/',
				'ttl' => 900
			];
		}

		parent::__construct($options);

		//-- Add plugins to cache system

		$this->addPlugin(new Serializer);

		$plugin = new ExceptionHandler;
		$plugin->setOptions(new PluginOptions([ 'throw_exceptions' => false ]));
		$this->addPlugin($plugin);
	}
}
