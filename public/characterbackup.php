<?php

use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Id\IdentityGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lotgd\Core\Event\Character;
use Symfony\Component\Filesystem\Filesystem;
use Tracy\Debugger;

require_once 'common.php';

check_su_access(SU_EDIT_USERS);

$op        = (string) LotgdRequest::getQuery('op');
$accountId = (int) LotgdRequest::getQuery('acctid');
$page      = (int) LotgdRequest::getQuery('page');

$params = [
    'textDomain' => 'grotto_characterbackup',
];

//-- Init page
LotgdResponse::pageStart('title.default', [], $params['textDomain']);

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addNav('navigation.nav.update', 'characterbackup.php', ['textDomain' => $params['textDomain']]);

$fileSystem = new Filesystem();
/** @var \Symfony\Component\Serializer\Serializer $serializer */
$serializer = LotgdKernel::get('serializer');
/** @var \Kit\CryptBundle\Service\OpensslService $cryptService */
$cryptService = LotgdKernel::get('Kit\CryptBundle\Service\OpensslService');

$path              = 'storage/logd_snapshots';
$pathAccountData   = "{$path}/user-{$accountId}/LotgdCore_User.json";
$pathCharacterData = "{$path}/user-{$accountId}/LotgdCore_Avatar.json";

if ('delete' == $op)
{
    $message = 'flash.message.del.error';

    if (file_exists("{$path}/user-{$accountId}"))
    {
        $fileSystem->remove("{$path}/user-{$accountId}");
        $message = 'flash.message.del.success';
    }

    LotgdFlashMessages::addInfoMessage(LotgdTranslator::t($message, ['path' => "{$path}/user-{$accountId}"], $params['textDomain']));

    $op = '';
    LotgdRequest::setQuery('op', '');
}
elseif ('restore' == $op && file_exists($pathAccountData) && file_exists($pathCharacterData))
{
    $files = glob("{$path}/user-{$accountId}/[!basic_info]*.json", GLOB_BRACE);
    //-- Remove Account and character, is proccess individualy
    unset($files[array_search($pathAccountData, $files)], $files[array_search($pathCharacterData, $files)]);

    $files = array_map(function ($path) use ($serializer)
    {
        return $serializer->decode(file_get_contents($path), 'json');
    }, $files);

    // $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
    // $hydrator->removeNamingStrategy(); //-- With this keyValue is keyValue. Otherwise it would be key_value

    //-- Overrides the automatic generation of IDs (avoid to change id of account and character)
    $metadataAcct = Doctrine::getClassMetadata('LotgdCore:User');
    $metadataAcct->setIdGenerator(new AssignedGenerator());
    $metadataAcct->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
    $metadataChar = Doctrine::getClassMetadata('LotgdCore:Avatar');
    $metadataChar->setIdGenerator(new AssignedGenerator());
    $metadataChar->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

    //-- Restore account and character
    $account   = $serializer->decode($cryptService->decrypt(file_get_contents($pathAccountData)), 'json');
    $character = $serializer->decode(file_get_contents($pathCharacterData), 'json');

    $account   = $serializer->denormalize($account['rows'][0], $account['entity']);
    $character = $serializer->denormalize($character['rows'][0], $character['entity']);

    $account->setAvatar(null);
    $character->setAcct(null);
    Doctrine::persist($account);
    Doctrine::persist($character);
    Doctrine::flush();

    $account->setAvatar($character);
    $character->setAcct($account);
    Doctrine::flush();

    //-- Restore automatic generation of IDs
    $metadataAcct->setIdGenerator(new IdentityGenerator());
    $metadataAcct->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_IDENTITY);
    $metadataChar->setIdGenerator(new IdentityGenerator());
    $metadataChar->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_IDENTITY);

    //-- Restore other entities
    foreach ($files as $file)
    {
        //-- Not do nothing if not have rows.
        if ( ! isset($file['rows']) || (isset($file['rows']) && empty($file['rows'])))
        {
            continue;
        }

        //-- Overrides the automatic generation of IDs
        $metadata = Doctrine::getClassMetadata($file['entity']);
        $metadata->setIdGenerator(new AssignedGenerator());
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $args = new Character([
            'entity'     => $file['entity'],
            'accountId'  => $accountId,
            'data'       => $file,
            'proccessed' => false,
        ]);
        LotgdEventDispatcher::dispatch($args, Character::BACKUP_RESTORE);
        $result = modulehook('character-restore', $args->getData());

        //-- Do nothing if it has been processed
        if ( ! isset($result['proccessed']) || ! $result['proccessed'])
        {
            try
            {
                $repository = Doctrine::getRepository($result['entity']);

                foreach ($result['rows'] as $row)
                {
                    Doctrine::persist($repository->hydrateEntity($row));
                }
            }
            catch (Throwable $th)
            {
                Debugger::log($th);
            }
        }

        //-- Restore automatic generation of IDs
        $metadata = Doctrine::getClassMetadata($file['entity']);
        $metadata->setIdGenerator(new IdentityGenerator());
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_IDENTITY);

        Doctrine::flush();
    }

    //-- Remove backup
    $fileSystem->remove("{$path}/user-{$accountId}");

    $op = '';
    LotgdRequest::setQuery('op', '');
}
elseif ('restore' == $op && ( ! file_exists($pathAccountData) || ! file_exists($pathCharacterData)))
{
    LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.restore.miss.mandatory', [], $params['textDomain']));

    $op = '';
    LotgdRequest::setQuery('op', '');
}

if ('' == $op)
{
    $params['tpl'] = 'default';

    $dirs = glob("{$path}/user-*", GLOB_ONLYDIR);
    //-- Clean directories invalid (not have basic_info.json, LotgdCore_Accounts.json or LotgdCore_Characters.json )
    $dirs = array_filter($dirs, function ($dir) use ($fileSystem)
    {
        if ( ! file_exists("{$dir}/basic_info.json") //-- Need for manage restore
        || ! file_exists("{$dir}/LotgdCore_User.json") //-- Is basic for restore an account
        || ! file_exists("{$dir}/LotgdCore_Avatar.json") //-- Is basic for restore an account
        ) {
            $fileSystem->remove($dir);

            return false;
        }

        return true;
    });

    $params['backups'] = array_map(function ($path) use ($serializer, $cryptService)
    {
        return $serializer->decode($cryptService->decrypt(file_get_contents("{$path}/basic_info.json")), 'json');
    }, $dirs);
}
elseif ('view' == $op)
{
    $params['tpl'] = 'view';
    $files         = glob("{$path}/user-{$accountId}/[!basic_info]*.json", GLOB_BRACE);

    $params['account'] = $serializer->decode($cryptService->decrypt(file_get_contents("{$path}/user-{$accountId}/basic_info.json")), 'json');
    $params['files']   = array_map(function ($path) use ($serializer, $cryptService)
    {
        $content = file_get_contents($path);
        $content = $cryptService->decrypt($content) ?: $content;
        $content = $serializer->decode($content, 'json');

        //-- Not show password of User entity
        if (isset($content['rows'][0]['password']))
        {
            unset($content['rows'][0]['password']);
        }

        return $content;
    }, $files);
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/characterbackup.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
