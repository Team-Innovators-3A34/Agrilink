<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304192924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demandes CHANGE expire_date expire_date DATE DEFAULT NULL, CHANGE propositon propositon VARCHAR(255) DEFAULT NULL, CHANGE reponse reponse VARCHAR(255) DEFAULT NULL, CHANGE nomdemandeur nomdemandeur VARCHAR(255) DEFAULT NULL, CHANGE nomowner nomowner VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event CHANGE lien_meet lien_meet VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE posts CHANGE images images JSON DEFAULT NULL, CHANGE likes likes JSON DEFAULT NULL, CHANGE dislikes dislikes JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation CHANGE date date DATETIME DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reponses CHANGE date date DATETIME DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE solution solution VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ressources CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE marque marque VARCHAR(255) DEFAULT NULL, CHANGE etat etat VARCHAR(255) DEFAULT NULL, CHANGE prix_location prix_location NUMERIC(10, 2) DEFAULT NULL, CHANGE superficie superficie DOUBLE PRECISION DEFAULT NULL, CHANGE images images JSON DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL, CHANGE reset_token_expires_at reset_token_expires_at DATETIME DEFAULT NULL, CHANGE verification_code verification_code VARCHAR(255) DEFAULT NULL, CHANGE code_expiration_date code_expiration_date DATETIME DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE bio bio VARCHAR(255) DEFAULT NULL, CHANGE country country VARCHAR(255) DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE longitude longitude VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude VARCHAR(255) DEFAULT NULL, CHANGE lock_until lock_until DATETIME DEFAULT NULL, CHANGE code2_fa code2_fa VARCHAR(255) DEFAULT NULL, CHANGE code2_faexpiry code2_faexpiry DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demandes CHANGE expire_date expire_date DATE DEFAULT \'NULL\', CHANGE propositon propositon VARCHAR(255) DEFAULT \'NULL\', CHANGE reponse reponse VARCHAR(255) DEFAULT \'NULL\', CHANGE nomdemandeur nomdemandeur VARCHAR(255) DEFAULT \'NULL\', CHANGE nomowner nomowner VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE event CHANGE lien_meet lien_meet VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE posts CHANGE images images LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE likes likes LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE dislikes dislikes LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE reclamation CHANGE date date DATETIME DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reponses CHANGE date date DATETIME DEFAULT \'NULL\', CHANGE status status VARCHAR(255) DEFAULT \'NULL\', CHANGE solution solution VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE ressources CHANGE type type VARCHAR(255) DEFAULT \'NULL\', CHANGE marque marque VARCHAR(255) DEFAULT \'NULL\', CHANGE etat etat VARCHAR(255) DEFAULT \'NULL\', CHANGE prix_location prix_location NUMERIC(10, 2) DEFAULT \'NULL\', CHANGE superficie superficie DOUBLE PRECISION DEFAULT \'NULL\', CHANGE images images LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE reset_token reset_token VARCHAR(255) DEFAULT \'NULL\', CHANGE reset_token_expires_at reset_token_expires_at DATETIME DEFAULT \'NULL\', CHANGE verification_code verification_code VARCHAR(255) DEFAULT \'NULL\', CHANGE code_expiration_date code_expiration_date DATETIME DEFAULT \'NULL\', CHANGE description description VARCHAR(255) DEFAULT \'NULL\', CHANGE bio bio VARCHAR(255) DEFAULT \'NULL\', CHANGE country country VARCHAR(255) DEFAULT \'NULL\', CHANGE city city VARCHAR(255) DEFAULT \'NULL\', CHANGE longitude longitude VARCHAR(255) DEFAULT \'NULL\', CHANGE latitude latitude VARCHAR(255) DEFAULT \'NULL\', CHANGE lock_until lock_until DATETIME DEFAULT \'NULL\', CHANGE code2_fa code2_fa VARCHAR(255) DEFAULT \'NULL\', CHANGE code2_faexpiry code2_faexpiry DATETIME DEFAULT \'NULL\'');
    }
}
