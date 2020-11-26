<?php

use Lotgd\Core\Fixed\Cache as LotgdCache;
use Lotgd\Core\Fixed\Dbwrapper as DB;
use Lotgd\Core\Fixed\Doctrine;
use Lotgd\Core\Fixed\EventManager as LotgdEvent;
use Lotgd\Core\Fixed\HookManager as LotgdHook;
use Lotgd\Core\Fixed\FlashMessages as LotgdFlashMessages;
use Lotgd\Core\Fixed\Format as LotgdFormat;
use Lotgd\Core\Fixed\Locator as LotgdLocator;
use Lotgd\Core\Fixed\Navigation as LotgdNavigation;
use Lotgd\Core\Fixed\Request as LotgdRequest;
use Lotgd\Core\Fixed\Response as LotgdReponse;
use Lotgd\Core\Fixed\Sanitize as LotgdSanitize;
use Lotgd\Core\Fixed\Theme as LotgdTheme;
use Lotgd\Core\Fixed\Translator as LotgdTranslator;

//-- Configure DB
DB::wrapper(LotgdLocator::get(Lotgd\Core\Db\Dbwrapper::class));

//-- Configure Doctrine
Doctrine::wrapper(LotgdLocator::get(\Lotgd\Core\Db\Doctrine::class));

//-- Configure Flash Messages
LotgdFlashMessages::setContainer(LotgdLocator::get(\Lotgd\Core\Component\FlashMessages::class));

//-- Configure format instance
LotgdFormat::instance(LotgdLocator::get(\Lotgd\Core\Output\Format::class));

//-- Configure Request instance
LotgdRequest::instance(LotgdLocator::get(\Lotgd\Core\Http\Request::class));

//-- Configure Response instance
LotgdReponse::instance(LotgdLocator::get(\Lotgd\Core\Http\Response::class));

//-- Configure Navigation instance
LotgdNavigation::instance(LotgdLocator::get(\Lotgd\Core\Navigation\Navigation::class));

//-- Configure Theme template
LotgdTheme::wrapper(LotgdLocator::get(\Lotgd\Core\Template\Theme::class));

//-- Configure Sanitize instance
LotgdSanitize::instance(LotgdLocator::get(\Lotgd\Core\Tool\Sanitize::class));

//-- Configure Translator
LotgdTranslator::setContainer(LotgdLocator::get(\Lotgd\Core\Translator\Translator::class));

//-- Configure Cache instance
LotgdCache::instance(LotgdLocator::get('Cache\Core\Lotgd'));

//-- Configure Hook Manager instance
LotgdHook::instance(LotgdLocator::get(\Lotgd\Core\EventManager\Hook::class));

//-- Configure Event Manager instance
LotgdEvent::instance(LotgdLocator::get(\Lotgd\Core\EventManager\Event::class));
