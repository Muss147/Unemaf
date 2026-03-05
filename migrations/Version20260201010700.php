<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260201010700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, active TINYINT NOT NULL, date_activity DATETIME NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, type_id INT DEFAULT NULL, couverture_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, update_user_id INT DEFAULT NULL, INDEX IDX_AC74095AC54C8C93 (type_id), UNIQUE INDEX UNIQ_AC74095A3F0A9AF5 (couverture_id), INDEX IDX_AC74095AE104C1D3 (created_user_id), INDEX IDX_AC74095AE0DFCA6C (update_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, type_id INT DEFAULT NULL, fichier_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, update_user_id INT DEFAULT NULL, INDEX IDX_A2B07288C54C8C93 (type_id), UNIQUE INDEX UNIQ_A2B07288F915CFE (fichier_id), INDEX IDX_A2B07288E104C1D3 (created_user_id), INDEX IDX_A2B07288E0DFCA6C (update_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE info (id INT AUTO_INCREMENT NOT NULL, texte LONGTEXT NOT NULL, lien VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_user_id INT DEFAULT NULL, update_user_id INT DEFAULT NULL, INDEX IDX_CB893157E104C1D3 (created_user_id), INDEX IDX_CB893157E0DFCA6C (update_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, affichage VARCHAR(255) DEFAULT NULL, active TINYINT NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, menu_id INT DEFAULT NULL, couverture_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, update_user_id INT DEFAULT NULL, INDEX IDX_140AB620CCD7E912 (menu_id), UNIQUE INDEX UNIQ_140AB6203F0A9AF5 (couverture_id), INDEX IDX_140AB620E104C1D3 (created_user_id), INDEX IDX_140AB620E0DFCA6C (update_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE parametres (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, presentation VARCHAR(255) DEFAULT NULL, description TEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, image_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, update_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_1A79799D3DA5256D (image_id), INDEX IDX_1A79799D727ACA70 (parent_id), INDEX IDX_1A79799DE104C1D3 (created_user_id), INDEX IDX_1A79799DE0DFCA6C (update_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE photos (id INT AUTO_INCREMENT NOT NULL, alt VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, source VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_user_id INT DEFAULT NULL, update_user_id INT DEFAULT NULL, INDEX IDX_876E0D9E104C1D3 (created_user_id), INDEX IDX_876E0D9E0DFCA6C (update_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE slider (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, lien VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, image_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, update_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_CFC710073DA5256D (image_id), INDEX IDX_CFC71007E104C1D3 (created_user_id), INDEX IDX_CFC71007E0DFCA6C (update_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, contact INT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, avatar_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D64986383B10 (avatar_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AC54C8C93 FOREIGN KEY (type_id) REFERENCES parametres (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A3F0A9AF5 FOREIGN KEY (couverture_id) REFERENCES photos (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AE104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AE0DFCA6C FOREIGN KEY (update_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288C54C8C93 FOREIGN KEY (type_id) REFERENCES parametres (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288F915CFE FOREIGN KEY (fichier_id) REFERENCES photos (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288E0DFCA6C FOREIGN KEY (update_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE info ADD CONSTRAINT FK_CB893157E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE info ADD CONSTRAINT FK_CB893157E0DFCA6C FOREIGN KEY (update_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620CCD7E912 FOREIGN KEY (menu_id) REFERENCES parametres (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6203F0A9AF5 FOREIGN KEY (couverture_id) REFERENCES photos (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620E0DFCA6C FOREIGN KEY (update_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE parametres ADD CONSTRAINT FK_1A79799D3DA5256D FOREIGN KEY (image_id) REFERENCES photos (id)');
        $this->addSql('ALTER TABLE parametres ADD CONSTRAINT FK_1A79799D727ACA70 FOREIGN KEY (parent_id) REFERENCES parametres (id)');
        $this->addSql('ALTER TABLE parametres ADD CONSTRAINT FK_1A79799DE104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE parametres ADD CONSTRAINT FK_1A79799DE0DFCA6C FOREIGN KEY (update_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D9E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D9E0DFCA6C FOREIGN KEY (update_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC710073DA5256D FOREIGN KEY (image_id) REFERENCES photos (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC71007E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC71007E0DFCA6C FOREIGN KEY (update_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES photos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AC54C8C93');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A3F0A9AF5');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AE104C1D3');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AE0DFCA6C');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288C54C8C93');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288F915CFE');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288E104C1D3');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288E0DFCA6C');
        $this->addSql('ALTER TABLE info DROP FOREIGN KEY FK_CB893157E104C1D3');
        $this->addSql('ALTER TABLE info DROP FOREIGN KEY FK_CB893157E0DFCA6C');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620CCD7E912');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6203F0A9AF5');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620E104C1D3');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620E0DFCA6C');
        $this->addSql('ALTER TABLE parametres DROP FOREIGN KEY FK_1A79799D3DA5256D');
        $this->addSql('ALTER TABLE parametres DROP FOREIGN KEY FK_1A79799D727ACA70');
        $this->addSql('ALTER TABLE parametres DROP FOREIGN KEY FK_1A79799DE104C1D3');
        $this->addSql('ALTER TABLE parametres DROP FOREIGN KEY FK_1A79799DE0DFCA6C');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D9E104C1D3');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D9E0DFCA6C');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC710073DA5256D');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC71007E104C1D3');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC71007E0DFCA6C');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE info');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE parametres');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE slider');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
