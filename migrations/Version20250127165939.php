<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127165939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agriculteur (id INT AUTO_INCREMENT NOT NULL, ressource VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CE85F12B8 (post_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE complaints (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, complaint_id INT NOT NULL, subject VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A05AAF3A9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demandes (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, demande_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expire_date DATE NOT NULL, status VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, INDEX IDX_BD940CBB9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, name VARCHAR(255) NOT NULL, date DATE NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_5387574ABCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE investors (id INT AUTO_INCREMENT NOT NULL, capital_dispo DOUBLE PRECISION NOT NULL, domaine VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, type VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, INDEX IDX_885DBAFA9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage_investors (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage_point (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, max_capacite INT NOT NULL, adresse VARCHAR(255) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, INDEX IDX_BDA771B07E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage_rapport (id INT AUTO_INCREMENT NOT NULL, recycling_point_id INT NOT NULL, date DATE NOT NULL, quantity INT NOT NULL, INDEX IDX_A8AF078CA43DDD27 (recycling_point_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage_rapport_recycled_produit (recyclage_rapport_id INT NOT NULL, recycled_produit_id INT NOT NULL, INDEX IDX_97BD27BD87460FC2 (recyclage_rapport_id), INDEX IDX_97BD27BD3D3A4699 (recycled_produit_id), PRIMARY KEY(recyclage_rapport_id, recycled_produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recycled_produit (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, complaint_id_id INT NOT NULL, response_id INT NOT NULL, content VARCHAR(255) NOT NULL, date_response DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_3E7B0BFB3E9FE5E1 (complaint_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource_investors (id INT AUTO_INCREMENT NOT NULL, ressource_agricol VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, phone_number INT NOT NULL, bio VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, experience VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE85F12B8 FOREIGN KEY (post_id_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demandes ADD CONSTRAINT FK_BD940CBB9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recyclage_point ADD CONSTRAINT FK_BDA771B07E3C61F9 FOREIGN KEY (owner_id) REFERENCES recyclage_investors (id)');
        $this->addSql('ALTER TABLE recyclage_rapport ADD CONSTRAINT FK_A8AF078CA43DDD27 FOREIGN KEY (recycling_point_id) REFERENCES recyclage_point (id)');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit ADD CONSTRAINT FK_97BD27BD87460FC2 FOREIGN KEY (recyclage_rapport_id) REFERENCES recyclage_rapport (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit ADD CONSTRAINT FK_97BD27BD3D3A4699 FOREIGN KEY (recycled_produit_id) REFERENCES recycled_produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB3E9FE5E1 FOREIGN KEY (complaint_id_id) REFERENCES complaints (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE85F12B8');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A9D86650F');
        $this->addSql('ALTER TABLE demandes DROP FOREIGN KEY FK_BD940CBB9D86650F');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574ABCF5E72D');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA9D86650F');
        $this->addSql('ALTER TABLE recyclage_point DROP FOREIGN KEY FK_BDA771B07E3C61F9');
        $this->addSql('ALTER TABLE recyclage_rapport DROP FOREIGN KEY FK_A8AF078CA43DDD27');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit DROP FOREIGN KEY FK_97BD27BD87460FC2');
        $this->addSql('ALTER TABLE recyclage_rapport_recycled_produit DROP FOREIGN KEY FK_97BD27BD3D3A4699');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFB3E9FE5E1');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE agriculteur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE complaints');
        $this->addSql('DROP TABLE demandes');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE investors');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE recyclage_investors');
        $this->addSql('DROP TABLE recyclage_point');
        $this->addSql('DROP TABLE recyclage_rapport');
        $this->addSql('DROP TABLE recyclage_rapport_recycled_produit');
        $this->addSql('DROP TABLE recycled_produit');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE ressource_investors');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
