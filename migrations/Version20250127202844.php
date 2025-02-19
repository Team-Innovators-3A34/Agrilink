<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127202844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE85F12B8');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A9D86650F');
        $this->addSql('ALTER TABLE demandes DROP FOREIGN KEY FK_BD940CBB9D86650F');
        $this->addSql('ALTER TABLE demandes_ressources DROP FOREIGN KEY FK_B57BECB2F49DCC2D');
        $this->addSql('ALTER TABLE demandes_ressources DROP FOREIGN KEY FK_B57BECB23C361826');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574ABCF5E72D');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA9D86650F');
        $this->addSql('ALTER TABLE recyclage_point DROP FOREIGN KEY FK_BDA771B07E3C61F9');
        $this->addSql('ALTER TABLE recyclage_rapport DROP FOREIGN KEY FK_A8AF078CA43DDD27');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit DROP FOREIGN KEY FK_97BD27BD87460FC2');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit DROP FOREIGN KEY FK_97BD27BD3D3A4699');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFB3E9FE5E1');
        $this->addSql('ALTER TABLE ressources DROP FOREIGN KEY FK_6A2CD5C78FDDAB70');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE agriculteur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE complaints');
        $this->addSql('DROP TABLE demandes');
        $this->addSql('DROP TABLE demandes_ressources');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE investors');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE recyclage_investors');
        $this->addSql('DROP TABLE recyclage_point');
        $this->addSql('DROP TABLE recyclage_rapport');
        $this->addSql('DROP TABLE recyclage_rapport_recycled_produit');
        $this->addSql('DROP TABLE recycled_produit');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE ressources');
        $this->addSql('DROP TABLE ressource_investors');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE agriculteur (id INT AUTO_INCREMENT NOT NULL, ressource VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, content VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CE85F12B8 (post_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE complaints (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, complaint_id INT NOT NULL, subject VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A05AAF3A9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE demandes (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expire_date DATE NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, demande_id INT NOT NULL, INDEX IDX_BD940CBB9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE demandes_ressources (demandes_id INT NOT NULL, ressources_id INT NOT NULL, INDEX IDX_B57BECB2F49DCC2D (demandes_id), INDEX IDX_B57BECB23C361826 (ressources_id), PRIMARY KEY(demandes_id, ressources_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date DATE NOT NULL, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_5387574ABCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE investors (id INT AUTO_INCREMENT NOT NULL, capital_dispo DOUBLE PRECISION NOT NULL, domaine VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_885DBAFA9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE recyclage_investors (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE recyclage_point (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, max_capacite INT NOT NULL, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, INDEX IDX_BDA771B07E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE recyclage_rapport (id INT AUTO_INCREMENT NOT NULL, recycling_point_id INT NOT NULL, date DATE NOT NULL, quantity INT NOT NULL, INDEX IDX_A8AF078CA43DDD27 (recycling_point_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE recyclage_rapport_recycled_produit (recyclage_rapport_id INT NOT NULL, recycled_produit_id INT NOT NULL, INDEX IDX_97BD27BD3D3A4699 (recycled_produit_id), INDEX IDX_97BD27BD87460FC2 (recyclage_rapport_id), PRIMARY KEY(recyclage_rapport_id, recycled_produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE recycled_produit (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, categorie VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, complaint_id_id INT NOT NULL, response_id INT NOT NULL, content VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_response DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_3E7B0BFB3E9FE5E1 (complaint_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ressources (id INT AUTO_INCREMENT NOT NULL, owner_id_id INT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_6A2CD5C78FDDAB70 (owner_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ressource_investors (id INT AUTO_INCREMENT NOT NULL, ressource_agricol VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone_number INT NOT NULL, bio VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, experience VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE85F12B8 FOREIGN KEY (post_id_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demandes ADD CONSTRAINT FK_BD940CBB9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demandes_ressources ADD CONSTRAINT FK_B57BECB2F49DCC2D FOREIGN KEY (demandes_id) REFERENCES demandes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demandes_ressources ADD CONSTRAINT FK_B57BECB23C361826 FOREIGN KEY (ressources_id) REFERENCES ressources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recyclage_point ADD CONSTRAINT FK_BDA771B07E3C61F9 FOREIGN KEY (owner_id) REFERENCES recyclage_investors (id)');
        $this->addSql('ALTER TABLE recyclage_rapport ADD CONSTRAINT FK_A8AF078CA43DDD27 FOREIGN KEY (recycling_point_id) REFERENCES recyclage_point (id)');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit ADD CONSTRAINT FK_97BD27BD87460FC2 FOREIGN KEY (recyclage_rapport_id) REFERENCES recyclage_rapport (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit ADD CONSTRAINT FK_97BD27BD3D3A4699 FOREIGN KEY (recycled_produit_id) REFERENCES recycled_produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB3E9FE5E1 FOREIGN KEY (complaint_id_id) REFERENCES complaints (id)');
        $this->addSql('ALTER TABLE ressources ADD CONSTRAINT FK_6A2CD5C78FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
