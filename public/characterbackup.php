<?php

require_once 'common.php';

check_su_access(SU_EDIT_USERS);

$op = (string) \LotgdHttp::getQuery('op');
$accountId = (int) \LotgdHttp::getQuery('acctid');
$page = (int) \LotgdHttp::getQuery('page');

$params = [
    'textDomain' => 'page-characterbackup',
];

page_header('title.default', [], $params['textDomain']);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addNav('navigation.nav.update', 'characterbackup.php', [ 'textDomain' => $params['textDomain'] ]);

$fileSystem = new \Lotgd\Core\Component\Filesystem();
$serializer = new Zend\Serializer\Adapter\PhpSerialize();
$path = 'data/logd_snapshots';

if ('delete' == $op)
{
    $message = 'flash.message.del.error';
    if (file_exists("{$path}/account-{$accountId}"))
    {
        $fileSystem->remove("{$path}/account-{$accountId}");
        $message = 'flash.message.del.success';
    }

    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t($message, [ 'path' => "{$path}/account-{$accountId}" ], $params['textDomain']));

    $op = '';
    \LotgdHttp::setQuery('op', '');
}
elseif ('restore' == $op)
{
    $files = glob("{$path}/account-{$accountId}/[!basic_info]*.data", GLOB_BRACE);
    //-- Remove Account and character, is proccess individualy
    $key = array_search("{$path}/account-{$accountId}/LotgdCore_Accounts.data", $files);
    unset($files[$key]);
    $key = array_search("{$path}/account-{$accountId}/LotgdCore_Characters.data", $files);
    unset($files[$key]);

    $files = array_map(function ($path) use ($serializer)
    {
        return $serializer->unserialize(file_get_contents($path));
    }, $files);

    $hydrator = new \Zend\Hydrator\ClassMethods();
    $hydrator->removeNamingStrategy(); //-- With this keyValue is keyValue. Otherwise it would be key_value

    //-- Overrides the automatic generation of IDs (avoid to change id of account and character)
    $metadataAcct = \Doctrine::getClassMetadata('LotgdCore:Accounts');
    $metadataAcct->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    $metadataAcct->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
    $metadataChar = \Doctrine::getClassMetadata('LotgdCore:Characters');
    $metadataChar->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    $metadataChar->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

    //-- Restore account and character
    $account = $serializer->unserialize(file_get_contents("{$path}/account-{$accountId}/LotgdCore_Accounts.data"));
    $account = $hydrator->hydrate($account['rows'][0], new $account['entity']());
    $character = $serializer->unserialize(file_get_contents("{$path}/account-{$accountId}/LotgdCore_Characters.data"));
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
    foreach($files as $file)
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

        $repository = \Doctrine::getRepository($file['entity']);

        foreach($file['rows'] as $row)
        {
            \Doctrine::persist($repository->hydrateEntity($row));
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
    \LotgdHttp::setQuery('op', '');
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

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/characterbackup.twig', $params));

page_footer();
