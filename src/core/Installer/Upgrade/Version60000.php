<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Serializer\Adapter\PhpSerialize;
use Lotgd\Core\Installer\InstallerAbstract;
use Lotgd\Core\Tool\Backup;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Version60000 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 60000;
    protected $hasMigration   = 20210707115250;

    private $backup;
    private $serializer;

    public function __construct(EntityManagerInterface $doctrine, TranslatorInterface $translator, Backup $backup, SerializerInterface $serializer)
    {
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
        $this->backup     = $backup;
        $this->serializer = $serializer;
    }

    //-- Delete old files
    public function step0(): bool
    {
        return $this->removeFiles([
            $this->getProjectDir().'/lib/battle/',
            $this->getProjectDir().'/lib/addnews.php',
            $this->getProjectDir().'/lib/charcleanup.php',
            $this->getProjectDir().'/lib/checkban.php',
            $this->getProjectDir().'/lib/creaturefunctions.php',
            $this->getProjectDir().'/lib/buffs.php',
            $this->getProjectDir().'/lib/datetime.php',
            $this->getProjectDir().'/lib/deathmessage.php',
            $this->getProjectDir().'/lib/debuglog.php',
            $this->getProjectDir().'/lib/experience.php',
            $this->getProjectDir().'/lib/fightnav.php',
            $this->getProjectDir().'/lib/forestoutcomes.php',
            $this->getProjectDir().'/lib/gamelog.php',
            $this->getProjectDir().'/lib/increment_specialty.php',
            $this->getProjectDir().'/lib/playerfunctions.php',
            $this->getProjectDir().'/lib/saveuser.php',
            $this->getProjectDir().'/lib/settings.php',
            $this->getProjectDir().'/lib/substitute.php',
            $this->getProjectDir().'/lib/taunt.php',
            $this->getProjectDir().'/lib/tempstat.php',
        ]);
    }

    //-- Update backup files accounts
    public function step1(): bool
    {
        try
        {
            $path = 'storage/logd_snapshots';

            $dirs = glob("{$path}/account-*", GLOB_ONLYDIR);

            $phs = new PhpSerialize();
            $fs  = new Filesystem();

            foreach ($dirs as $dir)
            {
                $files = glob("{$dir}/*.data");

                foreach ($files as $file)
                {
                    $content = $phs->unserialize(file_get_contents($file));

                    //-- If file not have rows and not is basic_info ignore
                    if (false === stripos($file, 'basic_info') && empty($content['rows']))
                    {
                        continue;
                    }

                    $file = str_replace([
                        '/account-',
                        '/LotgdCore_Accounts.',
                        '/LotgdCore_Characters.',
                        '.data',
                    ], [
                        '/user-',
                        '/LotgdCore_User.',
                        '/LotgdCore_Avatar.',
                        '.json',
                    ], $file);

                    //-- Encrypt data of user and basic info
                    $encrypt = (false !== stripos($file, 'LotgdCore_User') || false !== stripos($file, 'basic_info'));

                    $content = $this->serializer->serialize($content, 'json', [
                        AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object)
                        {
                            $property = $this->doctrine->getClassMetadata(\get_class($object))->getSingleIdentifierFieldName();
                            $method = 'get'.ucfirst($property);

                            return $object->{$method}();
                        },
                    ]);

                    if ($encrypt)
                    {
                        $content = $this->backup->encryptContent($content);
                    }

                    $fs->dumpFile($file, $content, LOCK_EX);
                }
            }
        }
        catch (\Throwable $th)
        {
            return false;
        }

        return true;
    }
}
