<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.md
 * @author IDMarinas
 *
 * @since 6.0.0
 */

declare(strict_types=1);

namespace Lotgd\Bundle\CoreBundle\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210506112034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '6.0.0 IDMarinas Edition';
    }

    public function up(Schema $schema) : void
    {
        //-- Create/alter new tables
        $this->lotgdCreateTables();

        //-- Import old data to new tables
        $this->lotgdImportData();

        //-- Drop/alter old tables
        $this->lotgdDropTables();
    }

    private function lotgdCreateTables()
    {
        $this->addSql('ALTER TABLE characters DROP FOREIGN KEY FK_3A29410E9D02C3AF');
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EAC1136BE75');
        $this->addSql('CREATE TABLE avatar (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, clan_id INT UNSIGNED DEFAULT NULL, name VARCHAR(50) NOT NULL, sex SMALLINT DEFAULT 0 NOT NULL, strength INT UNSIGNED DEFAULT 10 NOT NULL, dexterity INT UNSIGNED DEFAULT 10 NOT NULL, intelligence INT UNSIGNED DEFAULT 10 NOT NULL, constitution INT UNSIGNED DEFAULT 10 NOT NULL, wisdom INT UNSIGNED DEFAULT 10 NOT NULL, specialty VARCHAR(20) NOT NULL, experience INT UNSIGNED DEFAULT 0 NOT NULL, gold INT UNSIGNED DEFAULT 0 NOT NULL, weapon VARCHAR(50) DEFAULT \'Fists\' NOT NULL, armor VARCHAR(50) DEFAULT \'T-Shirt\' NOT NULL, seenmaster TINYINT(1) DEFAULT \'0\' NOT NULL, level SMALLINT UNSIGNED DEFAULT 1 NOT NULL, defense INT UNSIGNED DEFAULT 0 NOT NULL, attack INT UNSIGNED DEFAULT 0 NOT NULL, alive TINYINT(1) DEFAULT \'1\' NOT NULL, goldinbank INT DEFAULT 0 NOT NULL, marriedto INT UNSIGNED DEFAULT 0 NOT NULL, spirits INT NOT NULL, hitpoints INT UNSIGNED DEFAULT 10 NOT NULL, maxhitpoints INT UNSIGNED DEFAULT 10 NOT NULL, permahitpoints INT DEFAULT 0 NOT NULL, gems INT DEFAULT 0 NOT NULL, weaponvalue INT DEFAULT 0 NOT NULL, armorvalue INT DEFAULT 0 NOT NULL, location VARCHAR(50) DEFAULT \'Degolburg\' NOT NULL, turns INT DEFAULT 10 NOT NULL, title VARCHAR(50) NOT NULL, badguy LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', companions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', allowednavs LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', resurrections INT UNSIGNED DEFAULT 0 NOT NULL, weapondmg INT NOT NULL, armordef INT NOT NULL, age INT UNSIGNED DEFAULT 0 NOT NULL, charm INT DEFAULT 0 NOT NULL, specialinc VARCHAR(50) NOT NULL, specialmisc VARCHAR(1000) NOT NULL, lastmotd DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, playerfights INT DEFAULT 3 NOT NULL, lasthit DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, seendragon TINYINT(1) DEFAULT \'0\' NOT NULL, dragonkills INT UNSIGNED DEFAULT 0 NOT NULL, restorepage VARCHAR(150) NOT NULL, hashorse INT DEFAULT 0, bufflist LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', dragonpoints LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', boughtroomtoday TINYINT(1) DEFAULT \'0\' NOT NULL, sentnotice TINYINT(1) DEFAULT \'0\' NOT NULL, pvpflag DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, transferredtoday SMALLINT DEFAULT 0 NOT NULL, soulpoints INT UNSIGNED DEFAULT 0 NOT NULL, gravefights INT UNSIGNED DEFAULT 0 NOT NULL, hauntedby VARCHAR(50) NOT NULL, deathpower INT UNSIGNED DEFAULT 0 NOT NULL, recentcomments DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, bio VARCHAR(255) NOT NULL, race VARCHAR(50) DEFAULT \'0\' NOT NULL, biotime DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, amountouttoday INT UNSIGNED DEFAULT 0 NOT NULL, pk TINYINT(1) DEFAULT \'0\' NOT NULL, dragonage INT UNSIGNED DEFAULT 0 NOT NULL, bestdragonage INT UNSIGNED DEFAULT 0 NOT NULL, ctitle VARCHAR(25) NOT NULL, slaydragon TINYINT(1) DEFAULT \'0\' NOT NULL, fedmount TINYINT(1) DEFAULT \'0\' NOT NULL, clanrank SMALLINT UNSIGNED DEFAULT 0 NOT NULL, clanjoindate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, chatloc VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1677722F5E237E06 (name), INDEX IDX_1677722FA76ED395 (user_id), INDEX IDX_1677722FBEAF84C8 (clan_id), INDEX name (name), INDEX level (level), INDEX alive (alive), INDEX lasthit (lasthit), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentary_comment (id INT AUTO_INCREMENT NOT NULL, thread_id VARCHAR(255) DEFAULT NULL, author_id INT UNSIGNED DEFAULT NULL, avatar_id INT UNSIGNED DEFAULT NULL, clan_id INT UNSIGNED DEFAULT NULL, ancestors VARCHAR(1024) NOT NULL, depth INT NOT NULL, created_at DATETIME NOT NULL, state INT NOT NULL, author_name VARCHAR(255) NOT NULL, command VARCHAR(20) NOT NULL, clan_rank SMALLINT UNSIGNED DEFAULT 0 NOT NULL, clan_name VARCHAR(255) NOT NULL, clan_name_short VARCHAR(50) NOT NULL, params LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', raw_body TEXT NOT NULL, uncesored_body TEXT NOT NULL, censored_words LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', body TEXT NOT NULL, INDEX IDX_30D5CE1FE2904019 (thread_id), INDEX IDX_30D5CE1FF675F31B (author_id), INDEX IDX_30D5CE1F86383B10 (avatar_id), INDEX IDX_30D5CE1FBEAF84C8 (clan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentary_thread (id VARCHAR(255) NOT NULL, permalink VARCHAR(255) NOT NULL, is_commentable TINYINT(1) NOT NULL, num_comments INT NOT NULL, last_comment_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, command VARCHAR(1024) NOT NULL, schedule VARCHAR(191) NOT NULL, description VARCHAR(191) NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX un_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_report (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, run_at DATETIME NOT NULL, run_time DOUBLE PRECISION NOT NULL, exit_code INT NOT NULL, output LONGTEXT NOT NULL, INDEX IDX_B6C6A7F5BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE petition (id INT UNSIGNED AUTO_INCREMENT NOT NULL, problem_type_id INT UNSIGNED DEFAULT NULL, close_user_id INT UNSIGNED DEFAULT NULL, avatar_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, avatar_name VARCHAR(120) DEFAULT NULL, user_of_avatar VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, description TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, status VARCHAR(255) DEFAULT \'unhandled\' NOT NULL COMMENT \'(DC2Type:petition_status_type_enum)\', ip_address VARCHAR(45) NOT NULL, close_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_FF598B03236E4CE0 (problem_type_id), INDEX IDX_FF598B03EFB7A494 (close_user_id), INDEX IDX_FF598B0386383B10 (avatar_id), INDEX IDX_FF598B03A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE petition_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5C4A8495989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE petition_type_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT UNSIGNED DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_C46E618B232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT UNSIGNED AUTO_INCREMENT NOT NULL, domain_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(1000) NOT NULL, type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:setting_type_enum)\', value VARCHAR(1000) NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_9F74B898115F0EE5 (domain_id), INDEX IDX_9F74B898A76ED395 (user_id), UNIQUE INDEX lotgd_settings_bundle_setting (domain_id, name, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting_domain (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, priority INT NOT NULL, enabled TINYINT(1) NOT NULL, read_only TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9D55E7365E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, referer_id INT UNSIGNED DEFAULT NULL, avatar_id INT UNSIGNED DEFAULT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', email VARCHAR(255) DEFAULT NULL, last_motd DATETIME DEFAULT NULL, referer_is_rewarded TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, last_connection DATETIME DEFAULT NULL, ip_address VARCHAR(45) NOT NULL, deleted_at DATETIME DEFAULT NULL, banned_until DATETIME DEFAULT NULL, donation INT UNSIGNED NOT NULL, donation_spent INT UNSIGNED NOT NULL, session_id VARCHAR(45) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64987C61384 (referer_id), UNIQUE INDEX UNIQ_8D93D64986383B10 (avatar_id), INDEX deleted_at_index (deleted_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FBEAF84C8 FOREIGN KEY (clan_id) REFERENCES clans (clanid)');
        $this->addSql('ALTER TABLE commentary_comment ADD CONSTRAINT FK_30D5CE1FE2904019 FOREIGN KEY (thread_id) REFERENCES commentary_thread (id)');
        $this->addSql('ALTER TABLE commentary_comment ADD CONSTRAINT FK_30D5CE1FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentary_comment ADD CONSTRAINT FK_30D5CE1F86383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id)');
        $this->addSql('ALTER TABLE commentary_comment ADD CONSTRAINT FK_30D5CE1FBEAF84C8 FOREIGN KEY (clan_id) REFERENCES clans (clanid)');
        $this->addSql('ALTER TABLE cron_report ADD CONSTRAINT FK_B6C6A7F5BE04EA9 FOREIGN KEY (job_id) REFERENCES cron_job (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE petition ADD CONSTRAINT FK_FF598B03236E4CE0 FOREIGN KEY (problem_type_id) REFERENCES petition_type (id)');
        $this->addSql('ALTER TABLE petition ADD CONSTRAINT FK_FF598B03EFB7A494 FOREIGN KEY (close_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE petition ADD CONSTRAINT FK_FF598B0386383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id)');
        $this->addSql('ALTER TABLE petition ADD CONSTRAINT FK_FF598B03A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE petition_type_translation ADD CONSTRAINT FK_C46E618B232D562B FOREIGN KEY (object_id) REFERENCES petition_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898115F0EE5 FOREIGN KEY (domain_id) REFERENCES setting_domain (id)');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64987C61384 FOREIGN KEY (referer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id)');
    }

    private function lotgdDropTables()
    {
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE accounts_everypage');
        $this->addSql('DROP TABLE accounts_output');
        $this->addSql('DROP TABLE characters');
        $this->addSql('DROP TABLE commentary');
        $this->addSql('DROP TABLE cronjob');
        $this->addSql('DROP TABLE module_event_hooks');
        $this->addSql('DROP TABLE module_hooks');
        $this->addSql('DROP TABLE module_objprefs');
        $this->addSql('DROP TABLE module_settings');
        $this->addSql('DROP TABLE module_userprefs');
        $this->addSql('DROP TABLE modules');
        $this->addSql('DROP TABLE petitions');
        $this->addSql('ALTER TABLE debuglog CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE debuglog_archive CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE gamelog CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE logdnet CHANGE lastping lastping DATETIME NOT NULL');
        $this->addSql('ALTER TABLE mail CHANGE sent sent DATETIME NOT NULL');
        $this->addSql('ALTER TABLE moderatedcomments CHANGE moddate moddate DATETIME NOT NULL');
        $this->addSql('ALTER TABLE paylog ADD user_id INT UNSIGNED DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE processdate processdate DATETIME NOT NULL');
        $this->addSql('ALTER TABLE paylog ADD CONSTRAINT FK_78FBAC5EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_78FBAC5EA76ED395 ON paylog (user_id)');
        $this->addSql('DROP INDEX uri ON referers');
        $this->addSql('CREATE INDEX uri ON referers (uri)');
    }

    private function lotgdImportData()
    {
        //-- Import characters to avatar
        $this->addSql('INSERT INTO avatar
        (id, name, sex, strength, dexterity, intelligence, constitution, wisdom, specialty, experience, gold, weapon, armor, seenmaster, level, defense, attack, alive, goldinbank, marriedto, spirits, hitpoints, maxhitpoints, permahitpoints, gems, weaponvalue, armorvalue, location, turns, title, badguy, companions, allowednavs, resurrections, weapondmg, armordef, age, charm, specialinc, specialmisc, lastmotd, playerfights, lasthit, seendragon, dragonkills, restorepage, hashorse, bufflist, dragonpoints, boughtroomtoday, sentnotice, pvpflag, transferredtoday, soulpoints, gravefights, hauntedby, deathpower, recentcomments, bio, race, biotime, amountouttoday, pk, dragonage, bestdragonage, ctitle, slaydragon, fedmount, clan_id, clanrank, clanjoindate, chatloc)
        SELECT id, playername, sex, strength, dexterity, intelligence, constitution, wisdom, specialty, experience, gold, weapon, armor, seenmaster, level, defense, attack, alive, goldinbank, marriedto, spirits, hitpoints, maxhitpoints, permahitpoints, gems, weaponvalue, armorvalue, location, turns, title, badguy, companions, allowednavs, resurrections, weapondmg, armordef, age, charm, specialinc, specialmisc, lastmotd, playerfights, lasthit, seendragon, dragonkills, restorepage, hashorse, bufflist, dragonpoints, boughtroomtoday, sentnotice, pvpflag, transferredtoday, soulpoints, gravefights, hauntedby, deathpower, recentcomments, bio, race, biotime, amountouttoday, pk, dragonage, bestdragonage, ctitle, slaydragon, fedmount, clanid, clanrank, clanjoindate, chatloc FROM `characters`');

        //-- Import accounts to user
        $this->addSql('INSERT INTO  user (id, avatar_id, username, password, email, created_at, updated_at, referer_is_rewarded, donation, donation_spent, ip_address)
        SELECT acctid, character_id, login, password, emailaddress, regdate, laston, refererawarded, donation, donationspent, lastip FROM accounts');
        $this->addSql('UPDATE user AS u SET
            u.roles = (SELECT IF(BIT_AND(a.superuser | 2097152) > 0,\'["ROLE_SUPER_ADMIN"]\', \'[]\') FROM `accounts` AS a WHERE a.acctid = u.id),
            u.is_verified = 1,
            u.referer_id = (SELECT IF(a.referer = 0, NULL, a.referer) FROM `accounts` AS a WHERE a.acctid = u.id),
            u.last_motd = (SELECT IF(a.lastmotd = \'0000-00-00 00:00:00\', NULL, a.lastmotd) FROM `accounts` AS a WHERE a.acctid = u.id)');

        //-- Update user_id of avatar table (this id is related to user table, so first need import user table)
        $this->addSql('UPDATE avatar AS a SET a.user_id = (SELECT c.acct_id FROM `characters` AS c WHERE c.id = a.id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentary_comment DROP FOREIGN KEY FK_30D5CE1F86383B10');
        $this->addSql('ALTER TABLE petition DROP FOREIGN KEY FK_FF598B0386383B10');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10');
        $this->addSql('ALTER TABLE commentary_comment DROP FOREIGN KEY FK_30D5CE1FE2904019');
        $this->addSql('ALTER TABLE cron_report DROP FOREIGN KEY FK_B6C6A7F5BE04EA9');
        $this->addSql('ALTER TABLE petition DROP FOREIGN KEY FK_FF598B03236E4CE0');
        $this->addSql('ALTER TABLE petition_type_translation DROP FOREIGN KEY FK_C46E618B232D562B');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898115F0EE5');
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA76ED395');
        $this->addSql('ALTER TABLE commentary_comment DROP FOREIGN KEY FK_30D5CE1FF675F31B');
        $this->addSql('ALTER TABLE paylog DROP FOREIGN KEY FK_78FBAC5EA76ED395');
        $this->addSql('ALTER TABLE petition DROP FOREIGN KEY FK_FF598B03EFB7A494');
        $this->addSql('ALTER TABLE petition DROP FOREIGN KEY FK_FF598B03A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64987C61384');
        $this->addSql('CREATE TABLE accounts (acctid INT UNSIGNED AUTO_INCREMENT NOT NULL, character_id INT UNSIGNED DEFAULT NULL, laston DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, password VARCHAR(32) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, loggedin TINYINT(1) DEFAULT \'0\' NOT NULL, superuser INT UNSIGNED DEFAULT 0 NOT NULL, login VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, lastmotd DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, locked TINYINT(1) DEFAULT \'0\' NOT NULL, lastip VARCHAR(40) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, uniqueid VARCHAR(32) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, boughtroomtoday TINYINT(1) DEFAULT \'0\' NOT NULL, emailaddress VARCHAR(128) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, replaceemail VARCHAR(128) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, emailvalidation VARCHAR(32) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, forgottenpassword VARCHAR(32) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, sentnotice TINYINT(1) DEFAULT \'0\' NOT NULL, prefs LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', transferredtoday SMALLINT UNSIGNED DEFAULT 0 NOT NULL, recentcomments DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, donation INT UNSIGNED DEFAULT 0 NOT NULL, donationspent INT UNSIGNED DEFAULT 0 NOT NULL, donationconfig LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', referer INT UNSIGNED DEFAULT 0 NOT NULL, refererawarded INT UNSIGNED DEFAULT 0 NOT NULL, banoverride TINYINT(1) DEFAULT \'0\', translatorlanguages VARCHAR(128) CHARACTER SET utf8 DEFAULT \'en\' NOT NULL COLLATE `utf8_unicode_ci`, amountouttoday INT UNSIGNED DEFAULT 0 NOT NULL, beta TINYINT(1) DEFAULT \'0\' NOT NULL, regdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, INDEX emailaddress (emailaddress), INDEX referer (referer), INDEX emailvalidation (emailvalidation), UNIQUE INDEX UNIQ_CAC89EACAA08CB10 (login), INDEX login (login), INDEX locked (locked, loggedin, laston), INDEX uniqueid (uniqueid), UNIQUE INDEX UNIQ_CAC89EAC1136BE75 (character_id), INDEX laston (laston), PRIMARY KEY(acctid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE accounts_everypage (acctid INT UNSIGNED NOT NULL, gentime DOUBLE PRECISION UNSIGNED NOT NULL, gentimecount INT UNSIGNED NOT NULL, gensize INT UNSIGNED NOT NULL, PRIMARY KEY(acctid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE accounts_output (acctid INT UNSIGNED NOT NULL, output MEDIUMBLOB NOT NULL, PRIMARY KEY(acctid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE characters (id INT UNSIGNED AUTO_INCREMENT NOT NULL, acct_id INT UNSIGNED DEFAULT NULL, name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, playername VARCHAR(40) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, sex SMALLINT DEFAULT 0 NOT NULL, strength INT UNSIGNED DEFAULT 10 NOT NULL, dexterity INT UNSIGNED DEFAULT 10 NOT NULL, intelligence INT UNSIGNED DEFAULT 10 NOT NULL, constitution INT UNSIGNED DEFAULT 10 NOT NULL, wisdom INT UNSIGNED DEFAULT 10 NOT NULL, specialty VARCHAR(20) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, experience INT UNSIGNED DEFAULT 0 NOT NULL, gold INT UNSIGNED DEFAULT 0 NOT NULL, weapon VARCHAR(50) CHARACTER SET utf8 DEFAULT \'Fists\' NOT NULL COLLATE `utf8_unicode_ci`, armor VARCHAR(50) CHARACTER SET utf8 DEFAULT \'T-Shirt\' NOT NULL COLLATE `utf8_unicode_ci`, seenmaster TINYINT(1) DEFAULT \'0\' NOT NULL, level SMALLINT UNSIGNED DEFAULT 1 NOT NULL, defense INT UNSIGNED DEFAULT 0 NOT NULL, attack INT UNSIGNED DEFAULT 0 NOT NULL, alive TINYINT(1) DEFAULT \'1\' NOT NULL, goldinbank INT DEFAULT 0 NOT NULL, marriedto INT UNSIGNED DEFAULT 0 NOT NULL, spirits INT NOT NULL, hitpoints INT UNSIGNED DEFAULT 10 NOT NULL, maxhitpoints INT UNSIGNED DEFAULT 10 NOT NULL, permahitpoints INT DEFAULT 0 NOT NULL, gems INT DEFAULT 0 NOT NULL, weaponvalue INT DEFAULT 0 NOT NULL, armorvalue INT DEFAULT 0 NOT NULL, location VARCHAR(50) CHARACTER SET utf8 DEFAULT \'Degolburg\' NOT NULL COLLATE `utf8_unicode_ci`, turns INT DEFAULT 10 NOT NULL, title VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, badguy LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', companions LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', allowednavs LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', resurrections INT UNSIGNED DEFAULT 0 NOT NULL, weapondmg INT NOT NULL, armordef INT NOT NULL, age INT UNSIGNED DEFAULT 0 NOT NULL, charm INT DEFAULT 0 NOT NULL, specialinc VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, specialmisc VARCHAR(1000) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, lastmotd DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, playerfights INT DEFAULT 3 NOT NULL, lasthit DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, seendragon TINYINT(1) DEFAULT \'0\' NOT NULL, dragonkills INT UNSIGNED DEFAULT 0 NOT NULL, restorepage VARCHAR(150) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, hashorse INT DEFAULT 0, bufflist LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', dragonpoints LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', boughtroomtoday TINYINT(1) DEFAULT \'0\' NOT NULL, sentnotice TINYINT(1) DEFAULT \'0\' NOT NULL, pvpflag DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, transferredtoday SMALLINT DEFAULT 0 NOT NULL, soulpoints INT UNSIGNED DEFAULT 0 NOT NULL, gravefights INT UNSIGNED DEFAULT 0 NOT NULL, hauntedby VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, deathpower INT UNSIGNED DEFAULT 0 NOT NULL, recentcomments DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, bio VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, race VARCHAR(50) CHARACTER SET utf8 DEFAULT \'0\' NOT NULL COLLATE `utf8_unicode_ci`, biotime DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, amountouttoday INT UNSIGNED DEFAULT 0 NOT NULL, pk TINYINT(1) DEFAULT \'0\' NOT NULL, dragonage INT UNSIGNED DEFAULT 0 NOT NULL, bestdragonage INT UNSIGNED DEFAULT 0 NOT NULL, ctitle VARCHAR(25) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, slaydragon TINYINT(1) DEFAULT \'0\' NOT NULL, fedmount TINYINT(1) DEFAULT \'0\' NOT NULL, clanid INT UNSIGNED DEFAULT 0, clanrank SMALLINT UNSIGNED DEFAULT 0 NOT NULL, clanjoindate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, chatloc VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX alive (alive), INDEX clanid (clanid), UNIQUE INDEX UNIQ_3A29410E6E6926DF (playername), INDEX name (name), INDEX lasthit (lasthit), UNIQUE INDEX UNIQ_3A29410E9D02C3AF (acct_id), INDEX level (level), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commentary (id INT UNSIGNED AUTO_INCREMENT NOT NULL, section VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, command VARCHAR(20) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, comment VARCHAR(1000) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, comment_raw VARCHAR(1000) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, postdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, extra LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', author INT UNSIGNED DEFAULT 0 NOT NULL, author_name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, clan_id INT UNSIGNED DEFAULT 0 NOT NULL, clan_rank SMALLINT UNSIGNED DEFAULT 0 NOT NULL, clan_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, clan_name_short VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, hidden TINYINT(1) DEFAULT \'0\' NOT NULL, hidden_comment VARCHAR(500) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, hidden_by INT UNSIGNED DEFAULT 0 NOT NULL, hidden_by_name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX section (section), INDEX hidden (hidden), INDEX postdate (postdate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cronjob (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, command TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, schedule VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, mailer VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, maxRuntime INT UNSIGNED DEFAULT NULL, smtpHost VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpPort SMALLINT DEFAULT NULL, smtpUsername VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpPassword VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpSender VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpSenderName VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpSecurity VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, runAs VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, environment TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, runOnHost VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, output VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, dateFormat VARCHAR(100) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, enabled TINYINT(1) DEFAULT NULL, haltDir VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, debug TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_A5DA7C8A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_event_hooks (event_type VARCHAR(20) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, event_chance TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX event_type (event_type), INDEX modulename (modulename), PRIMARY KEY(event_type, modulename)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_hooks (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, location VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, function VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, whenactive TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, priority INT DEFAULT 50 NOT NULL, INDEX location (location), PRIMARY KEY(modulename, location, function)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_objprefs (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, objtype VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, setting VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, objid INT UNSIGNED DEFAULT 0 NOT NULL, value TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(modulename, objtype, setting, objid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_settings (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, setting VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, value TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(modulename, setting)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_userprefs (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, setting VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, userid INT UNSIGNED DEFAULT 0 NOT NULL, value TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX modulename (modulename, userid), PRIMARY KEY(modulename, setting, userid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE modules (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, formalname VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, description TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, moduleauthor VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, active TINYINT(1) DEFAULT \'0\' NOT NULL, filename VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, installdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, installedby VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, filemoddate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, type TINYINT(1) DEFAULT \'0\' NOT NULL, extras TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, category VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, infokeys TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, version VARCHAR(10) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, download VARCHAR(200) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(modulename)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE petitions (petitionid INT UNSIGNED AUTO_INCREMENT NOT NULL, author INT UNSIGNED NOT NULL, date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, status SMALLINT UNSIGNED NOT NULL, body LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', pageinfo LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', closedate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, closeuserid INT UNSIGNED NOT NULL, ip VARCHAR(40) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, id VARCHAR(32) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(petitionid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC1136BE75 FOREIGN KEY (character_id) REFERENCES characters (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE characters ADD CONSTRAINT FK_3A29410E9D02C3AF FOREIGN KEY (acct_id) REFERENCES accounts (acctid) ON DELETE SET NULL');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE commentary_comment');
        $this->addSql('DROP TABLE commentary_thread');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_report');
        $this->addSql('DROP TABLE petition');
        $this->addSql('DROP TABLE petition_type');
        $this->addSql('DROP TABLE petition_type_translation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE setting_domain');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE debuglog CHANGE date date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('ALTER TABLE debuglog_archive CHANGE date date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('ALTER TABLE gamelog CHANGE date date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('ALTER TABLE logdnet CHANGE lastping lastping DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('ALTER TABLE mail CHANGE sent sent DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('ALTER TABLE moderatedcomments CHANGE moddate moddate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('DROP INDEX IDX_78FBAC5EA76ED395 ON paylog');
        $this->addSql('ALTER TABLE paylog DROP user_id, DROP created_at, DROP updated_at, CHANGE processdate processdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
        $this->addSql('DROP INDEX uri ON referers');
        $this->addSql('CREATE INDEX uri ON referers (uri(255))');
    }
}
