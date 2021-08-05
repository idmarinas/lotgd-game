<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210805104627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '6.1.0';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentary ADD translatable TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX uri ON referers');
        $this->addSql('CREATE INDEX uri ON referers (uri)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentary DROP translatable');
        $this->addSql('DROP INDEX uri ON referers');
        $this->addSql('CREATE INDEX uri ON referers (uri(255))');
    }
}
