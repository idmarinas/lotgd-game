<?php

use Lotgd\Core\Fixed\{
    Dbwrapper as DB,
    Doctrine,
    FlashMessages as LotgdFlashMessages,
    Format as LotgdFormat,
    Http as LotgdHttp,
    Locator as LotgdLocator,
    Navigation as LotgdNavigation,
    Sanitize as LotgdSanitize,
    Theme as LotgdTheme,
    Translator as LotgdTranslator
};

//-- Configure DB
DB::wrapper(LotgdLocator::get(Lotgd\Core\Db\Dbwrapper::class));

//-- Configure Doctrine
Doctrine::wrapper(LotgdLocator::get(\Lotgd\Core\Db\Doctrine::class));

//-- Configure Flash Messages
LotgdFlashMessages::setContainer(LotgdLocator::get(\Lotgd\Core\Component\FlashMessages::class));

//-- Configure format instance
LotgdFormat::instance(LotgdLocator::get(\Lotgd\Core\Output\Format::class));

//-- Configure Http instance
LotgdHttp::instance(LotgdLocator::get(\Lotgd\Core\Http::class));

//-- Configure Navigation instance
LotgdNavigation::instance(LotgdLocator::get(\Lotgd\Core\Navigation\Navigation::class));

//-- Configure Theme template
LotgdTheme::wrapper(LotgdLocator::get(\Lotgd\Core\Template\Theme::class));

//-- Configure Sanitize instance
LotgdSanitize::wrapper(LotgdLocator::get(\Lotgd\Core\Tool\Sanitize::class));

//-- Configure Translator
LotgdTranslator::setContainer(LotgdLocator::get(\Lotgd\Core\Translator\Translator::class));
