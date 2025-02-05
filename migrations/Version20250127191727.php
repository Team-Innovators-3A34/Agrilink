<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127191727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demandes_ressources (demandes_id INT NOT NULL, ressources_id INT NOT NULL, INDEX IDX_B57BECB2F49DCC2D (demandes_id), INDEX IDX_B57BECB23C361826 (ressources_id), PRIMARY KEY(demandes_id, ressources_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressources (id INT AUTO_INCREMENT NOT NULL, owner_id_id INT NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_6A2CD5C78FDDAB70 (owner_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demandes_ressources ADD CONSTRAINT FK_B57BECB2F49DCC2D FOREIGN KEY (demandes_id) REFERENCES demandes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demandes_ressources ADD CONSTRAINT FK_B57BECB23C361826 FOREIGN KEY (ressources_id) REFERENCES ressources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressources ADD CONSTRAINT FK_6A2CD5C78FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demandes ADD demande_id INT NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demandes_ressources DROP FOREIGN KEY FK_B57BECB2F49DCC2D');
        $this->addSql('ALTER TABLE demandes_ressources DROP FOREIGN KEY FK_B57BECB23C361826');
        $this->addSql('ALTER TABLE ressources DROP FOREIGN KEY FK_6A2CD5C78FDDAB70');
        $this->addSql('DROP TABLE demandes_ressources');
        $this->addSql('DROP TABLE ressources');
        $this->addSql('ALTER TABLE demandes DROP demande_id');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
