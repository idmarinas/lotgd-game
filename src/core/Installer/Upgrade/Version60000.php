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

use Throwable;
use Doctrine\ORM\EntityManagerInterface;
use Kit\CryptBundle\Service\OpensslService as Crypt;
use Laminas\Serializer\Adapter\PhpSerialize;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Version60000 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 60000;
    protected $hasMigration   = 20210707115250;

    private $crypt;
    private $serializer;

    public function __construct(EntityManagerInterface $doctrine, TranslatorInterface $translator, Crypt $crypt, SerializerInterface $serializer)
    {
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
        $this->crypt      = $crypt;
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
            $this->getProjectDir().'/public/battle.php',
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

                    if (false !== stripos($file, 'LotgdCore_Avatar'))
                    {
                        $content['entity'] = 'Lotgd\Core\Entity\Avatar';
                        $avatar = &$content['rows'][0];

                        $avatar['badguy'] = is_array($avatar['badguy']) ? $avatar['badguy'] : [];
                        $avatar['companions'] = is_array($avatar['companions']) ? $avatar['companions'] : [];
                        $avatar['allowednavs'] = is_array($avatar['allowednavs']) ? $avatar['allowednavs'] : [];
                        $avatar['bufflist'] = is_array($avatar['bufflist']) ? $avatar['bufflist'] : [];
                    }
                    elseif (false !== stripos($file, 'LotgdCore_User'))
                    {
                        $content['entity'] = 'Lotgd\Core\Entity\User';
                        $user = &$content['rows'][0];

                        $user['prefs'] = is_array($user['prefs']) ? $user['prefs'] : [];
                    }

                    $content = $this->serializer->serialize($content, 'json', [
                        AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object)
                        {
                            $property = $this->doctrine->getClassMetadata(\get_class($object))->getSingleIdentifierFieldName();
                            $method = 'get'.ucfirst($property);

                            return $object->{$method}();
                        },
                        AbstractNormalizer::CALLBACKS => $this->serializeCallbacks()
                    ]);

                    if ($encrypt)
                    {
                        $content = $this->crypt->encrypt($content);
                    }

                    $fs->dumpFile($file, $content);
                }
            }
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Update some fields
    public function step2(): bool
    {
        try
        {
            $this->doctrine->getConnection()->executeQuery(sprintf(
                "UPDATE `user` SET `prefs` = 'a:0:{}' WHERE `prefs` = '%s' OR `prefs` = '%s'",
                's:0:"";',
                'N;'
            ));
            $this->doctrine->getConnection()->executeQuery(sprintf(
                "UPDATE `avatar` SET `badguy` = 'a:0:{}' WHERE `badguy` = '%s' OR `badguy` = '%s'",
                's:0:"";',
                'N;'
            ));
            $this->doctrine->getConnection()->executeQuery(sprintf(
                "UPDATE `avatar` SET `companions` = 'a:0:{}' WHERE `companions` = '%s' OR `companions` = '%s'",
                's:0:"";',
                'N;'
            ));
            $this->doctrine->getConnection()->executeQuery(sprintf(
                "UPDATE `avatar` SET `allowednavs` = 'a:0:{}' WHERE `allowednavs` = '%s' OR `allowednavs` = '%s'",
                's:0:"";',
                'N;'
            ));
            $this->doctrine->getConnection()->executeQuery(sprintf(
                "UPDATE `avatar` SET `bufflist` = 'a:0:{}' WHERE `bufflist` = '%s' OR `bufflist` = '%s'",
                's:0:"";',
                'N;'
            ));
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    private function serializeCallbacks(): array
    {
        return [
            'prefs' => function ($innerObject, $outerObject)
            {
                if (is_array($outerObject->getPrefs()))
                {
                    return $outerObject->getPrefs();
                }

                return [];
            },
            'badguy' => function ($innerObject, $outerObject)
            {
                if (is_array($outerObject->getBadguy()))
                {
                    return $outerObject->getBadguy();
                }

                return [];
            },
            'companions' => function ($innerObject, $outerObject)
            {
                if (is_array($outerObject->getCompanions()))
                {
                    return $outerObject->getCompanions();
                }

                return [];
            },
            'allowednavs' => function ($innerObject, $outerObject)
            {
                if (is_array($outerObject->getAllowednavs()))
                {
                    return $outerObject->getAllowednavs();
                }

                return [];
            },
            'bufflist' => function ($innerObject, $outerObject)
            {
                if (is_array($outerObject->getBufflist()))
                {
                    return $outerObject->getBufflist();
                }

                return [];
            },
        ];
    }
}
