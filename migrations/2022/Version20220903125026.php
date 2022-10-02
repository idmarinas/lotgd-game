<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220903125026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '8.0.0';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cronjob');
        $this->addSql('DROP TABLE module_event_hooks');
        $this->addSql('DROP TABLE module_hooks');
        $this->addSql('DROP TABLE module_objprefs');
        $this->addSql('DROP TABLE module_settings');
        $this->addSql('DROP TABLE module_userprefs');
        $this->addSql('DROP TABLE modules');
        $this->addSql('DROP INDEX uri ON referers');
        $this->addSql('CREATE INDEX uri ON referers (uri)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cronjob (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, command TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, schedule VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, mailer VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, maxRuntime INT UNSIGNED DEFAULT NULL, smtpHost VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpPort SMALLINT DEFAULT NULL, smtpUsername VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpPassword VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpSender VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpSenderName VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, smtpSecurity VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, runAs VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, environment TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, runOnHost VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, output VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, dateFormat VARCHAR(100) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, enabled TINYINT(1) DEFAULT NULL, haltDir VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, debug TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_A5DA7C8A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_event_hooks (event_type VARCHAR(20) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, event_chance TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX modulename (modulename), INDEX event_type (event_type), PRIMARY KEY(event_type, modulename)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_hooks (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, location VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, function VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, whenactive TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, priority INT DEFAULT 50 NOT NULL, INDEX location (location), PRIMARY KEY(modulename, location, function)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_objprefs (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, objtype VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, setting VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, objid INT UNSIGNED DEFAULT 0 NOT NULL, value TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(modulename, objtype, setting, objid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_settings (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, setting VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, value TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(modulename, setting)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE module_userprefs (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, setting VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, userid INT UNSIGNED DEFAULT 0 NOT NULL, value TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX modulename (modulename, userid), PRIMARY KEY(modulename, setting, userid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE modules (modulename VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, formalname VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, description TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, moduleauthor VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, active TINYINT(1) DEFAULT \'0\' NOT NULL, filename VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, installdate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, installedby VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, filemoddate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, type TINYINT(1) DEFAULT \'0\' NOT NULL, extras TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, category VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, infokeys TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, version VARCHAR(10) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, download VARCHAR(200) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(modulename)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP INDEX uri ON referers');
        $this->addSql('CREATE INDEX uri ON referers (uri(255))');
    }
}
