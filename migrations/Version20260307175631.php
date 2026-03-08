<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307175631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY `FK_140AB620CCD7E912`');
        $this->addSql('DROP INDEX IDX_140AB620CCD7E912 ON page');
        $this->addSql('ALTER TABLE page DROP menu_id');
        $this->addSql('ALTER TABLE parametres ADD pages_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parametres ADD CONSTRAINT FK_1A79799D401ADD27 FOREIGN KEY (pages_id) REFERENCES page (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1A79799D401ADD27 ON parametres (pages_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page ADD menu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT `FK_140AB620CCD7E912` FOREIGN KEY (menu_id) REFERENCES parametres (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_140AB620CCD7E912 ON page (menu_id)');
        $this->addSql('ALTER TABLE parametres DROP FOREIGN KEY FK_1A79799D401ADD27');
        $this->addSql('DROP INDEX IDX_1A79799D401ADD27 ON parametres');
        $this->addSql('ALTER TABLE parametres DROP pages_id');
    }
}
