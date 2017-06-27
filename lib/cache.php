<?php

use Zend\Cache\Storage\Adapter\Filesystem;

use Zend\Cache\Storage\Plugin\PluginOptions;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\Plugin\ExceptionHandler;

class LotgdCache extends Filesystem
{
	public function __construct(array $options = [])
	{
		$default = [
			'namespace' => 'lotgd',
			'cache_dir' => 'cache/',
			'ttl' => 900,
            'key_pattern' => '/^[a-z0-9_\+\-\/\.]*$/Di'
		];

        $options = array_merge($default, $options);

		parent::__construct($options);

		//-- Add plugins to cache system

		$this->addPlugin(new Serializer);

		$plugin = new ExceptionHandler;
		$plugin->setOptions(new PluginOptions([ 'throw_exceptions' => false ]));
		$this->addPlugin($plugin);
	}
}
