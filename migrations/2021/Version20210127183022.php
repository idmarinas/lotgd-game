<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210127183022 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Clean install';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accounts (acctid INT UNSIGNED AUTO_INCREMENT NOT NULL, character_id INT UNSIGNED DEFAULT NULL, laston DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, password VARCHAR(32) NOT NULL, loggedin TINYINT(1) DEFAULT \'0\' NOT NULL, superuser INT UNSIGNED DEFAULT 0 NOT NULL, login VARCHAR(50) NOT NULL, lastmotd DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, locked TINYINT(1) DEFAULT \'0\' NOT NULL, lastip VARCHAR(40) NOT NULL, uniqueid VARCHAR(32) NOT NULL, boughtroomtoday TINYINT(1) DEFAULT \'0\' NOT NULL, emailaddress VARCHAR(128) NOT NULL, replaceemail VARCHAR(128) NOT NULL, emailvalidation VARCHAR(32) DEFAULT NULL, forgottenpassword VARCHAR(32) DEFAULT NULL, sentnotice TINYINT(1) DEFAULT \'0\' NOT NULL, prefs LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', transferredtoday SMALLINT UNSIGNED DEFAULT 0 NOT NULL, recentcomments DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, donation INT UNSIGNED DEFAULT 0 NOT NULL, donationspent INT UNSIGNED DEFAULT 0 NOT NULL, donationconfig LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', referer INT UNSIGNED DEFAULT 0 NOT NULL, refererawarded INT UNSIGNED DEFAULT 0 NOT NULL, banoverride TINYINT(1) DEFAULT \'0\', translatorlanguages VARCHAR(128) DEFAULT \'en\' NOT NULL, amountouttoday INT UNSIGNED DEFAULT 0 NOT NULL, beta TINYINT(1) DEFAULT \'0\' NOT NULL, regdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, UNIQUE INDEX UNIQ_CAC89EACAA08CB10 (login), UNIQUE INDEX UNIQ_CAC89EAC1136BE75 (character_id), INDEX login (login), INDEX laston (laston), INDEX emailaddress (emailaddress), INDEX locked (locked, loggedin, laston), INDEX referer (referer), INDEX uniqueid (uniqueid), INDEX emailvalidation (emailvalidation), PRIMARY KEY(acctid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounts_everypage (acctid INT UNSIGNED NOT NULL, gentime DOUBLE PRECISION UNSIGNED NOT NULL, gentimecount INT UNSIGNED NOT NULL, gensize INT UNSIGNED NOT NULL, PRIMARY KEY(acctid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounts_output (acctid INT UNSIGNED NOT NULL, output MEDIUMBLOB NOT NULL, PRIMARY KEY(acctid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE armor (armorid INT UNSIGNED AUTO_INCREMENT NOT NULL, armorname VARCHAR(128) DEFAULT NULL, value INT UNSIGNED NOT NULL, defense SMALLINT UNSIGNED DEFAULT 1 NOT NULL, level SMALLINT UNSIGNED NOT NULL, PRIMARY KEY(armorid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE armor_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT UNSIGNED DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_4ABA0F8B232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bans (uniqueid VARCHAR(32) NOT NULL, ipfilter VARCHAR(40) NOT NULL, banexpire DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, banreason TEXT NOT NULL, banner VARCHAR(50) NOT NULL, lasthit DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, PRIMARY KEY(uniqueid, ipfilter)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characters (id INT UNSIGNED AUTO_INCREMENT NOT NULL, acct_id INT UNSIGNED DEFAULT NULL, name VARCHAR(100) NOT NULL, playername VARCHAR(40) NOT NULL, sex SMALLINT DEFAULT 0 NOT NULL, strength INT UNSIGNED DEFAULT 10 NOT NULL, dexterity INT UNSIGNED DEFAULT 10 NOT NULL, intelligence INT UNSIGNED DEFAULT 10 NOT NULL, constitution INT UNSIGNED DEFAULT 10 NOT NULL, wisdom INT UNSIGNED DEFAULT 10 NOT NULL, specialty VARCHAR(20) NOT NULL, experience INT UNSIGNED DEFAULT 0 NOT NULL, gold INT UNSIGNED DEFAULT 0 NOT NULL, weapon VARCHAR(50) DEFAULT \'Fists\' NOT NULL, armor VARCHAR(50) DEFAULT \'T-Shirt\' NOT NULL, seenmaster TINYINT(1) DEFAULT \'0\' NOT NULL, level SMALLINT UNSIGNED DEFAULT 1 NOT NULL, defense INT UNSIGNED DEFAULT 0 NOT NULL, attack INT UNSIGNED DEFAULT 0 NOT NULL, alive TINYINT(1) DEFAULT \'1\' NOT NULL, goldinbank INT DEFAULT 0 NOT NULL, marriedto INT UNSIGNED DEFAULT 0 NOT NULL, spirits INT NOT NULL, hitpoints INT UNSIGNED DEFAULT 10 NOT NULL, maxhitpoints INT UNSIGNED DEFAULT 10 NOT NULL, permahitpoints INT DEFAULT 0 NOT NULL, gems INT DEFAULT 0 NOT NULL, weaponvalue INT DEFAULT 0 NOT NULL, armorvalue INT DEFAULT 0 NOT NULL, location VARCHAR(50) DEFAULT \'Degolburg\' NOT NULL, turns INT DEFAULT 10 NOT NULL, title VARCHAR(50) NOT NULL, badguy LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', companions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', allowednavs LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', resurrections INT UNSIGNED DEFAULT 0 NOT NULL, weapondmg INT NOT NULL, armordef INT NOT NULL, age INT UNSIGNED DEFAULT 0 NOT NULL, charm INT DEFAULT 0 NOT NULL, specialinc VARCHAR(50) NOT NULL, specialmisc VARCHAR(1000) NOT NULL, lastmotd DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, playerfights INT DEFAULT 3 NOT NULL, lasthit DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, seendragon TINYINT(1) DEFAULT \'0\' NOT NULL, dragonkills INT UNSIGNED DEFAULT 0 NOT NULL, restorepage VARCHAR(150) NOT NULL, hashorse INT DEFAULT 0, bufflist LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', dragonpoints LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', boughtroomtoday TINYINT(1) DEFAULT \'0\' NOT NULL, sentnotice TINYINT(1) DEFAULT \'0\' NOT NULL, pvpflag DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, transferredtoday SMALLINT DEFAULT 0 NOT NULL, soulpoints INT UNSIGNED DEFAULT 0 NOT NULL, gravefights INT UNSIGNED DEFAULT 0 NOT NULL, hauntedby VARCHAR(50) NOT NULL, deathpower INT UNSIGNED DEFAULT 0 NOT NULL, recentcomments DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, bio VARCHAR(255) NOT NULL, race VARCHAR(50) DEFAULT \'0\' NOT NULL, biotime DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, amountouttoday INT UNSIGNED DEFAULT 0 NOT NULL, pk TINYINT(1) DEFAULT \'0\' NOT NULL, dragonage INT UNSIGNED DEFAULT 0 NOT NULL, bestdragonage INT UNSIGNED DEFAULT 0 NOT NULL, ctitle VARCHAR(25) NOT NULL, slaydragon TINYINT(1) DEFAULT \'0\' NOT NULL, fedmount TINYINT(1) DEFAULT \'0\' NOT NULL, clanid INT UNSIGNED DEFAULT 0, clanrank SMALLINT UNSIGNED DEFAULT 0 NOT NULL, clanjoindate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, chatloc VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3A29410E6E6926DF (playername), UNIQUE INDEX UNIQ_3A29410E9D02C3AF (acct_id), INDEX name (name), INDEX level (level), INDEX alive (alive), INDEX lasthit (lasthit), INDEX clanid (clanid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clans (clanid INT UNSIGNED AUTO_INCREMENT NOT NULL, clanname VARCHAR(255) NOT NULL, clanshort VARCHAR(50) NOT NULL, clanmotd TEXT NOT NULL, clandesc TEXT NOT NULL, motdauthor INT UNSIGNED DEFAULT 0 NOT NULL, descauthor INT UNSIGNED DEFAULT 0 NOT NULL, customsay VARCHAR(15) NOT NULL, INDEX clanname (clanname), INDEX clanshort (clanshort), PRIMARY KEY(clanid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentary (id INT UNSIGNED AUTO_INCREMENT NOT NULL, section VARCHAR(50) NOT NULL, command VARCHAR(20) NOT NULL, comment VARCHAR(1000) NOT NULL, comment_raw VARCHAR(1000) NOT NULL, postdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, extra LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', author INT UNSIGNED DEFAULT 0 NOT NULL, author_name VARCHAR(100) NOT NULL, clan_id INT UNSIGNED DEFAULT 0 NOT NULL, clan_rank SMALLINT UNSIGNED DEFAULT 0 NOT NULL, clan_name VARCHAR(255) NOT NULL, clan_name_short VARCHAR(50) NOT NULL, hidden TINYINT(1) DEFAULT \'0\' NOT NULL, hidden_comment VARCHAR(500) NOT NULL, hidden_by INT UNSIGNED DEFAULT 0 NOT NULL, hidden_by_name VARCHAR(100) NOT NULL, INDEX section (section), INDEX postdate (postdate), INDEX hidden (hidden), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE companions (companionid INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, category VARCHAR(50) NOT NULL, description TEXT NOT NULL, attack SMALLINT UNSIGNED DEFAULT 1 NOT NULL, attackperlevel SMALLINT UNSIGNED DEFAULT 0 NOT NULL, defense SMALLINT UNSIGNED DEFAULT 1 NOT NULL, defenseperlevel SMALLINT UNSIGNED DEFAULT 0 NOT NULL, maxhitpoints SMALLINT UNSIGNED DEFAULT 10 NOT NULL, maxhitpointsperlevel SMALLINT UNSIGNED DEFAULT 10 NOT NULL, abilities LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', cannotdie TINYINT(1) DEFAULT \'0\' NOT NULL, cannotbehealed TINYINT(1) DEFAULT \'1\' NOT NULL, companionlocation VARCHAR(25) NOT NULL, companionactive TINYINT(1) DEFAULT \'1\' NOT NULL, companioncostdks INT DEFAULT 0 NOT NULL, companioncostgems INT UNSIGNED DEFAULT 0 NOT NULL, companioncostgold INT UNSIGNED DEFAULT 0 NOT NULL, jointext TEXT NOT NULL, dyingtext VARCHAR(255) NOT NULL, allowinshades TINYINT(1) DEFAULT \'0\' NOT NULL, allowinpvp TINYINT(1) DEFAULT \'0\' NOT NULL, allowintrain TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(companionid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE companions_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_48A3236232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE creatures (creatureid INT UNSIGNED AUTO_INCREMENT NOT NULL, creaturename VARCHAR(50) DEFAULT NULL, creaturecategory VARCHAR(50) DEFAULT NULL, creatureimage VARCHAR(250) NOT NULL, creaturedescription TEXT NOT NULL, creatureweapon VARCHAR(50) DEFAULT NULL, creaturegoldbonus NUMERIC(4, 2) NOT NULL, creatureattackbonus NUMERIC(4, 2) NOT NULL, creaturedefensebonus NUMERIC(4, 2) NOT NULL, creaturehealthbonus NUMERIC(4, 2) NOT NULL, creaturelose VARCHAR(120) DEFAULT NULL, creaturewin VARCHAR(120) DEFAULT NULL, creatureaiscript TEXT DEFAULT NULL, createdby VARCHAR(50) DEFAULT NULL, forest TINYINT(1) DEFAULT \'0\' NOT NULL, graveyard TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX creaturecategory (creaturecategory), PRIMARY KEY(creatureid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE creatures_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT UNSIGNED DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_83B847BC232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cronjob (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE `utf8_general_ci`, command TEXT NOT NULL, schedule VARCHAR(255) NOT NULL, mailer VARCHAR(255) DEFAULT NULL, maxRuntime INT UNSIGNED DEFAULT NULL, smtpHost VARCHAR(255) DEFAULT NULL, smtpPort SMALLINT DEFAULT NULL, smtpUsername VARCHAR(255) DEFAULT NULL, smtpPassword VARCHAR(255) DEFAULT NULL, smtpSender VARCHAR(255) DEFAULT NULL, smtpSenderName VARCHAR(255) DEFAULT NULL, smtpSecurity VARCHAR(255) DEFAULT NULL, runAs VARCHAR(255) DEFAULT NULL, environment TEXT DEFAULT NULL, runOnHost VARCHAR(255) DEFAULT NULL, output VARCHAR(255) DEFAULT NULL, dateFormat VARCHAR(100) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, haltDir VARCHAR(255) DEFAULT NULL, debug TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_A5DA7C8A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE debug (id BIGINT AUTO_INCREMENT NOT NULL, type VARCHAR(100) DEFAULT NULL, category VARCHAR(100) DEFAULT NULL, subcategory VARCHAR(100) DEFAULT NULL, value VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE debuglog (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, actor INT UNSIGNED DEFAULT NULL, target INT UNSIGNED DEFAULT NULL, message TEXT NOT NULL, field VARCHAR(20) NOT NULL, value DOUBLE PRECISION DEFAULT \'0.00\' NOT NULL, INDEX date (date), INDEX target (target), INDEX field (actor, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE debuglog_archive (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, actor INT UNSIGNED DEFAULT NULL, target INT UNSIGNED DEFAULT NULL, message TEXT NOT NULL, field VARCHAR(20) NOT NULL, value DOUBLE PRECISION DEFAULT \'0.00\' NOT NULL, INDEX date (date), INDEX target (target), INDEX field (actor, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faillog (eventid INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, post LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ip VARCHAR(40) NOT NULL, acctid INT UNSIGNED DEFAULT NULL, id VARCHAR(32) NOT NULL, INDEX date (date), INDEX acctid (acctid), INDEX ip (ip), PRIMARY KEY(eventid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gamelog (logid INT UNSIGNED AUTO_INCREMENT NOT NULL, message TEXT NOT NULL, category VARCHAR(50) NOT NULL, filed TINYINT(1) NOT NULL, date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, who INT UNSIGNED NOT NULL, INDEX date (category, date), PRIMARY KEY(logid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logdnet (serverid INT UNSIGNED AUTO_INCREMENT NOT NULL, address VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, priority DOUBLE PRECISION DEFAULT \'100\' NOT NULL, lastupdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, version VARCHAR(255) DEFAULT \'Unknown\' NOT NULL, admin VARCHAR(255) DEFAULT \'unknown\' NOT NULL, lastping DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, recentips VARCHAR(255) NOT NULL, count INT UNSIGNED NOT NULL, lang VARCHAR(20) NOT NULL, PRIMARY KEY(serverid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logdnetbans (banid INT UNSIGNED AUTO_INCREMENT NOT NULL, bantype VARCHAR(20) NOT NULL, banvalue VARCHAR(255) NOT NULL, PRIMARY KEY(banid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail (messageid INT UNSIGNED AUTO_INCREMENT NOT NULL, msgfrom INT UNSIGNED NOT NULL, msgto INT UNSIGNED NOT NULL, subject VARCHAR(500) NOT NULL, body TEXT NOT NULL, sent DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, seen TINYINT(1) DEFAULT \'0\' NOT NULL, originator INT UNSIGNED NOT NULL, INDEX msgto (msgto), INDEX seen (seen), PRIMARY KEY(messageid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE masters (creatureid INT UNSIGNED AUTO_INCREMENT NOT NULL, creaturename VARCHAR(50) DEFAULT NULL, creaturelevel INT UNSIGNED DEFAULT NULL, creatureweapon VARCHAR(50) DEFAULT NULL, creaturelose VARCHAR(120) DEFAULT NULL, creaturewin VARCHAR(120) DEFAULT NULL, PRIMARY KEY(creatureid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE masters_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT UNSIGNED DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_5EC87A80232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moderatedcomments (modid INT UNSIGNED AUTO_INCREMENT NOT NULL, comment TEXT NOT NULL, moderator INT UNSIGNED DEFAULT 0 NOT NULL, moddate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, PRIMARY KEY(modid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_event_hooks (event_type VARCHAR(20) NOT NULL, modulename VARCHAR(50) NOT NULL, event_chance TEXT NOT NULL, INDEX modulename (modulename), INDEX event_type (event_type), PRIMARY KEY(event_type, modulename)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_hooks (modulename VARCHAR(50) NOT NULL, `location` VARCHAR(50) NOT NULL, `function` VARCHAR(50) NOT NULL, whenactive TEXT NOT NULL, priority INT DEFAULT 50 NOT NULL, INDEX location (location), PRIMARY KEY(modulename, location, function)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_objprefs (modulename VARCHAR(50) NOT NULL, objtype VARCHAR(50) NOT NULL, setting VARCHAR(50) NOT NULL, objid INT UNSIGNED DEFAULT 0 NOT NULL, value TEXT NOT NULL, PRIMARY KEY(modulename, objtype, setting, objid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_settings (modulename VARCHAR(50) NOT NULL, setting VARCHAR(50) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(modulename, setting)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_userprefs (modulename VARCHAR(50) NOT NULL, setting VARCHAR(50) NOT NULL, userid INT UNSIGNED DEFAULT 0 NOT NULL, value TEXT NOT NULL, INDEX modulename (modulename, userid), PRIMARY KEY(modulename, setting, userid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modules (modulename VARCHAR(50) NOT NULL, formalname VARCHAR(255) NOT NULL, description TEXT NOT NULL, moduleauthor VARCHAR(255) NOT NULL, active TINYINT(1) DEFAULT \'0\' NOT NULL, filename VARCHAR(255) NOT NULL, installdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, installedby VARCHAR(50) NOT NULL, filemoddate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, type TINYINT(1) DEFAULT \'0\' NOT NULL, extras TEXT DEFAULT NULL, category VARCHAR(50) NOT NULL, infokeys TEXT NOT NULL, version VARCHAR(10) NOT NULL, download VARCHAR(200) NOT NULL, PRIMARY KEY(modulename)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motd (motditem INT UNSIGNED AUTO_INCREMENT NOT NULL, motdtitle VARCHAR(200) DEFAULT NULL, motdbody TEXT NOT NULL, motddate DATETIME DEFAULT \'0000-00-00 00:00:00\', motdtype TINYINT(1) DEFAULT \'0\' NOT NULL, motdauthor INT UNSIGNED DEFAULT 0 NOT NULL, PRIMARY KEY(motditem)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mounts (mountid INT UNSIGNED AUTO_INCREMENT NOT NULL, mountname VARCHAR(50) NOT NULL, mountdesc TEXT DEFAULT NULL, mountcategory VARCHAR(50) NOT NULL, mountbuff LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', mountcostgems INT UNSIGNED DEFAULT 0 NOT NULL, mountcostgold INT UNSIGNED DEFAULT 0 NOT NULL, mountactive TINYINT(1) DEFAULT \'1\' NOT NULL, mountforestfights INT DEFAULT 0 NOT NULL, newday TEXT NOT NULL, recharge TEXT NOT NULL, partrecharge TEXT NOT NULL, mountfeedcost INT UNSIGNED DEFAULT 20 NOT NULL, mountlocation VARCHAR(25) DEFAULT \'all\' NOT NULL, mountdkcost INT UNSIGNED DEFAULT 0 NOT NULL, PRIMARY KEY(mountid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mounts_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT UNSIGNED DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_A992825E232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATE DEFAULT \'0000-00-00\' NOT NULL, text TEXT NOT NULL, account_id INT UNSIGNED NOT NULL, arguments LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', new_format TINYINT(1) DEFAULT \'1\' NOT NULL, text_domain VARCHAR(255) DEFAULT \'partial_news\' NOT NULL, INDEX account_id (account_id), INDEX date (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paylog (payid INT UNSIGNED AUTO_INCREMENT NOT NULL, info LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', response TEXT NOT NULL, txnid VARCHAR(32) NOT NULL, amount DOUBLE PRECISION NOT NULL, name VARCHAR(50) NOT NULL, acctid INT UNSIGNED NOT NULL, processed TINYINT(1) NOT NULL, filed TINYINT(1) NOT NULL, txfee DOUBLE PRECISION NOT NULL, processdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, INDEX txnid (txnid), PRIMARY KEY(payid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE petitions (petitionid INT UNSIGNED AUTO_INCREMENT NOT NULL, author INT UNSIGNED NOT NULL, date DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, status SMALLINT UNSIGNED NOT NULL, body LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', pageinfo LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', closedate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, closeuserid INT UNSIGNED NOT NULL, ip VARCHAR(40) NOT NULL, id VARCHAR(32) NOT NULL, PRIMARY KEY(petitionid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pollresults (resultid INT UNSIGNED AUTO_INCREMENT NOT NULL, choice INT UNSIGNED NOT NULL, account INT UNSIGNED NOT NULL, motditem INT UNSIGNED NOT NULL, UNIQUE INDEX vote (account, motditem), PRIMARY KEY(resultid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referers (refererid INT UNSIGNED AUTO_INCREMENT NOT NULL, uri VARCHAR(1000) NOT NULL, count INT NOT NULL, last DATETIME NOT NULL, site VARCHAR(50) NOT NULL, dest VARCHAR(255) NOT NULL, ip VARCHAR(40) NOT NULL, INDEX uri (uri), INDEX site (site), PRIMARY KEY(refererid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (setting VARCHAR(25) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(setting)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE titles (titleid INT UNSIGNED AUTO_INCREMENT NOT NULL, dk INT UNSIGNED NOT NULL, ref VARCHAR(100) NOT NULL, male VARCHAR(25) NOT NULL, female VARCHAR(25) NOT NULL, INDEX dk (dk), PRIMARY KEY(titleid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE titles_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT UNSIGNED DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_25558CB9232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weapons (weaponid INT UNSIGNED AUTO_INCREMENT NOT NULL, weaponname VARCHAR(128) DEFAULT NULL, value INT UNSIGNED NOT NULL, damage SMALLINT UNSIGNED DEFAULT 1 NOT NULL, level SMALLINT UNSIGNED NOT NULL, PRIMARY KEY(weaponid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weapons_translation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_id INT UNSIGNED DEFAULT NULL, content LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, INDEX IDX_7231892232D562B (object_id), UNIQUE INDEX lookup_unique_idx (locale, object_id, field), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE whostyping (name VARCHAR(255) NOT NULL COLLATE `utf8_general_ci`, time INT UNSIGNED NOT NULL, section VARCHAR(255) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC1136BE75 FOREIGN KEY (character_id) REFERENCES characters (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE armor_translation ADD CONSTRAINT FK_4ABA0F8B232D562B FOREIGN KEY (object_id) REFERENCES armor (armorid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE characters ADD CONSTRAINT FK_3A29410E9D02C3AF FOREIGN KEY (acct_id) REFERENCES accounts (acctid) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE companions_translation ADD CONSTRAINT FK_48A3236232D562B FOREIGN KEY (object_id) REFERENCES companions (companionid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE creatures_translation ADD CONSTRAINT FK_83B847BC232D562B FOREIGN KEY (object_id) REFERENCES creatures (creatureid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE masters_translation ADD CONSTRAINT FK_5EC87A80232D562B FOREIGN KEY (object_id) REFERENCES masters (creatureid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mounts_translation ADD CONSTRAINT FK_A992825E232D562B FOREIGN KEY (object_id) REFERENCES mounts (mountid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE titles_translation ADD CONSTRAINT FK_25558CB9232D562B FOREIGN KEY (object_id) REFERENCES titles (titleid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE weapons_translation ADD CONSTRAINT FK_7231892232D562B FOREIGN KEY (object_id) REFERENCES weapons (weaponid) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE characters DROP FOREIGN KEY FK_3A29410E9D02C3AF');
        $this->addSql('ALTER TABLE armor_translation DROP FOREIGN KEY FK_4ABA0F8B232D562B');
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EAC1136BE75');
        $this->addSql('ALTER TABLE companions_translation DROP FOREIGN KEY FK_48A3236232D562B');
        $this->addSql('ALTER TABLE creatures_translation DROP FOREIGN KEY FK_83B847BC232D562B');
        $this->addSql('ALTER TABLE masters_translation DROP FOREIGN KEY FK_5EC87A80232D562B');
        $this->addSql('ALTER TABLE mounts_translation DROP FOREIGN KEY FK_A992825E232D562B');
        $this->addSql('ALTER TABLE titles_translation DROP FOREIGN KEY FK_25558CB9232D562B');
        $this->addSql('ALTER TABLE weapons_translation DROP FOREIGN KEY FK_7231892232D562B');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE accounts_everypage');
        $this->addSql('DROP TABLE accounts_output');
        $this->addSql('DROP TABLE armor');
        $this->addSql('DROP TABLE armor_translation');
        $this->addSql('DROP TABLE bans');
        $this->addSql('DROP TABLE characters');
        $this->addSql('DROP TABLE clans');
        $this->addSql('DROP TABLE commentary');
        $this->addSql('DROP TABLE companions');
        $this->addSql('DROP TABLE companions_translation');
        $this->addSql('DROP TABLE creatures');
        $this->addSql('DROP TABLE creatures_translation');
        $this->addSql('DROP TABLE cronjob');
        $this->addSql('DROP TABLE debug');
        $this->addSql('DROP TABLE debuglog');
        $this->addSql('DROP TABLE debuglog_archive');
        $this->addSql('DROP TABLE faillog');
        $this->addSql('DROP TABLE gamelog');
        $this->addSql('DROP TABLE logdnet');
        $this->addSql('DROP TABLE logdnetbans');
        $this->addSql('DROP TABLE mail');
        $this->addSql('DROP TABLE masters');
        $this->addSql('DROP TABLE masters_translation');
        $this->addSql('DROP TABLE moderatedcomments');
        $this->addSql('DROP TABLE module_event_hooks');
        $this->addSql('DROP TABLE module_hooks');
        $this->addSql('DROP TABLE module_objprefs');
        $this->addSql('DROP TABLE module_settings');
        $this->addSql('DROP TABLE module_userprefs');
        $this->addSql('DROP TABLE modules');
        $this->addSql('DROP TABLE motd');
        $this->addSql('DROP TABLE mounts');
        $this->addSql('DROP TABLE mounts_translation');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE paylog');
        $this->addSql('DROP TABLE petitions');
        $this->addSql('DROP TABLE pollresults');
        $this->addSql('DROP TABLE referers');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE titles');
        $this->addSql('DROP TABLE titles_translation');
        $this->addSql('DROP TABLE weapons');
        $this->addSql('DROP TABLE weapons_translation');
        $this->addSql('DROP TABLE whostyping');
    }
}
