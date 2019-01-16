<?php

use Lotgd\Core\Fixed\{
    Dbwrapper as DB,
    Doctrine,
    Format as LotgdFormat,
    Locator as LotgdLocator,
    Theme as LotgdTheme
};

//-- Prepare service manager
LotgdLocator::setServiceManager(new \Lotgd\Core\ServiceManager());

//-- Configure DB
DB::wrapper(LotgdLocator::get(Lotgd\Core\Db\Dbwrapper::class));

//-- Configure Doctrine
Doctrine::wrapper(LotgdLocator::get(\Lotgd\Core\Db\Doctrine::class));

//-- Configure format instance
LotgdFormat::instance(LotgdLocator::get(\Lotgd\Core\Output\Format::class));

//-- Configure Theme template
LotgdTheme::wrapper(LotgdLocator::get(\Lotgd\Core\Template\Theme::class));

