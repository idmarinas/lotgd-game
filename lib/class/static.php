<?php

use Lotgd\Core\Fixed\{
    Dbwrapper as DB,
    Doctrine,
    FlashMessages as LotgdFlashMessages,
    Format as LotgdFormat,
    Locator as LotgdLocator,
    Theme as LotgdTheme,
    Translator as LotgdTranslator
};

//-- Prepare service manager
LotgdLocator::setServiceManager(new \Lotgd\Core\ServiceManager());

//-- Configure DB
DB::wrapper(LotgdLocator::get(Lotgd\Core\Db\Dbwrapper::class));

//-- Configure Doctrine
Doctrine::wrapper(LotgdLocator::get(\Lotgd\Core\Db\Doctrine::class));

//-- Configure Flash Messages
LotgdFlashMessages::setContainer(LotgdLocator::get(\Lotgd\Core\Component\FlashMessages::class));

//-- Configure format instance
LotgdFormat::instance(LotgdLocator::get(\Lotgd\Core\Output\Format::class));

//-- Configure Theme template
LotgdTheme::wrapper(LotgdLocator::get(\Lotgd\Core\Template\Theme::class));

//-- Configure Translator
LotgdTranslator::setContainer(LotgdLocator::get(\Lotgd\Core\Translator\Translator::class));
