<?php

use Lotgd\Core\Event\Character;

require_once 'common.php';

check_su_access(SU_EDIT_USERS);

$op = (string) \LotgdRequest::getQuery('op');
$accountId = (int) \LotgdRequest::getQuery('acctid');
$page = (int) \LotgdRequest::getQuery('page');

$params = [
    'textDomain' => 'grotto_characterbackup',
];

//-- Init page
\LotgdResponse::pageStart('title.default', [], $params['textDomain']);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addNav('navigation.nav.update', 'characterbackup.php', ['textDomain' => $params['textDomain']]);

$fileSystem = new \Symfony\Component\Filesystem\Filesystem();
$serializer = new Laminas\Serializer\Adapter\PhpSerialize();
$path = 'storage/logd_snapshots';
$pathAccountData = "{$path}/account-{$accountId}/LotgdCore_Accounts.data";
$pathCharacterData = "{$path}/account-{$accountId}/LotgdCore_Characters.data";

if ('delete' == $op)
{
    $message = 'flash.message.del.error';

    if (file_exists("{$path}/account-{$accountId}"))
    {
        $fileSystem->remove("{$path}/account-{$accountId}");
        $message = 'flash.message.del.success';
    }

    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t($message, ['path' => "{$path}/account-{$accountId}"], $params['textDomain']));

    $op = '';
    \LotgdRequest::setQuery('op', '');
}
elseif ('restore' == $op && \file_exists($pathAccountData) && \file_exists($pathCharacterData))
{
    $files = glob("{$path}/account-{$accountId}/[!basic_info]*.data", GLOB_BRACE);
    //-- Remove Account and character, is proccess individualy
    $key = array_search($pathAccountData, $files);
    unset($files[$key]);
    $key = array_search($pathCharacterData, $files);
    unset($files[$key]);

    $files = array_map(function ($path) use ($serializer)
    {
        $file = $serializer->unserialize(file_get_contents($path));
        $file['shortNameEntity'] = str_replace('_', ':', basename($path, '.data'));

        return $file;
    }, $files);

    $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
    $hydrator->removeNamingStrategy(); //-- With this keyValue is keyValue. Otherwise it would be key_value

    //-- Overrides the automatic generation of IDs (avoid to change id of account and character)
    $metadataAcct = \Doctrine::getClassMetadata('LotgdCore:User');
    $metadataAcct->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    $metadataAcct->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
    $metadataChar = \Doctrine::getClassMetadata('LotgdCore:Characters');
    $metadataChar->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    $metadataChar->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

    //-- Restore account and character
    $account = $serializer->unserialize(file_get_contents($pathAccountData));
    $account = $hydrator->hydrate($account['rows'][0], new $account['entity']());
    $character = $serializer->unserialize(file_get_contents($pathCharacterData));
    $character = $hydrator->hydrate($character['rows'][0], new  $character['entity']());

    $account->setCharacter(null);
    \Doctrine::persist($account);
    \Doctrine::flush();

    $character->setAcct($account);
    \Doctrine::persist($character);
    \Doctrine::flush();

    $account->setCharacter($character);
    \Doctrine::persist($account);
    \Doctrine::flush();

    //-- Restore automatic generation of IDs
    $metadataAcct->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
    $metadataAcct->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_IDENTITY);
    $metadataChar->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
    $metadataChar->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_IDENTITY);

    //-- First restore account and character
    foreach ($files as $file)
    {
        //-- Not do nothing if not have rows.
        if (isset($file['rows']) && empty($file['rows']))
        {
            continue;
        }

        //-- Overrides the automatic generation of IDs
        $metadata = \Doctrine::getClassMetadata($file['entity']);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $args = new Character([
            'entity' => $file['shortNameEntity'],
            'accountId' => $accountId,
            'data' => $file,
            'proccessed' => false
        ]);
        \LotgdEventDispatcher::dispatch($args, Character::BACKUP_RESTORE);
        $result = modulehook('character-restore', $args->getData());

        //-- Do nothing if it has been processed
        if (! isset($result['proccessed']) || ! $result['proccessed'])
        {
            try
            {
                $repository = \Doctrine::getRepository($file['entity']);

                foreach ($file['rows'] as $row)
                {
                    \Doctrine::persist($repository->hydrateEntity($row));
                }
            }
            catch (\Throwable $th)
            {
                \Tracy\Debugger::log($th);
            }
        }

        //-- Restore automatic generation of IDs
        $metadata = \Doctrine::getClassMetadata($file['entity']);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_IDENTITY);

        \Doctrine::flush();
    }

    //-- Remove backup
    $fileSystem->remove("{$path}/account-{$accountId}");

    $op = '';
    \LotgdRequest::setQuery('op', '');
}
elseif ('restore' == $op && (! \file_exists($pathAccountData) || ! \file_exists($pathCharacterData)))
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.restore.miss.mandatory', [], $params['textDomain']));

    $op = '';
    \LotgdRequest::setQuery('op', '');
}

if ('' == $op)
{
    $params['tpl'] = 'default';

    $dirs = glob("{$path}/account-*", GLOB_ONLYDIR);
    //-- Clean directories invalid (not have basic_info.data, LotgdCore_Accounts.data or LotgdCore_Characters.data )
    foreach ($dirs as $key => $dir)
    {
        if (! \file_exists("{$dir}/basic_info.data") //-- Need for manage restore
            || ! \file_exists("{$dir}/LotgdCore_Accounts.data") //-- Is basic for restore an account
            || ! \file_exists("{$dir}/LotgdCore_Characters.data") //-- Is basic for restore an account
        ) {
            $fileSystem->remove($dir);
            unset($dirs[$key]);
        }
    }

    $params['backups'] = array_map(function ($path) use ($serializer)
    {
        return $serializer->unserialize(file_get_contents("{$path}/basic_info.data"));
    }, $dirs);
}
elseif ('view' == $op)
{
    $params['tpl'] = 'view';
    $files = glob("{$path}/account-{$accountId}/[!basic_info]*.data", GLOB_BRACE);

    $params['account'] = $serializer->unserialize(file_get_contents("{$path}/account-{$accountId}/basic_info.data"));
    $params['files'] = array_map(function ($path) use ($serializer)
    {
        return $serializer->unserialize(file_get_contents($path));
    }, $files);
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/characterbackup.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
