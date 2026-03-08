<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307181055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parametres DROP FOREIGN KEY `FK_1A79799D401ADD27`');
        $this->addSql('DROP INDEX IDX_1A79799D401ADD27 ON parametres');
        $this->addSql('ALTER TABLE parametres CHANGE pages_id page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parametres ADD CONSTRAINT FK_1A79799DC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1A79799DC4663E4 ON parametres (page_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parametres DROP FOREIGN KEY FK_1A79799DC4663E4');
        $this->addSql('DROP INDEX IDX_1A79799DC4663E4 ON parametres');
        $this->addSql('ALTER TABLE parametres CHANGE page_id pages_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parametres ADD CONSTRAINT `FK_1A79799D401ADD27` FOREIGN KEY (pages_id) REFERENCES page (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1A79799D401ADD27 ON parametres (pages_id)');
    }
}
