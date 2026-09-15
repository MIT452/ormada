<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260515112650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actualite (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(200) NOT NULL, contenu LONGTEXT NOT NULL, publierle DATETIME NOT NULL, dejapublier TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(200) NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, creerle DATETIME NOT NULL, dejalu TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(220) NOT NULL, description LONGTEXT NOT NULL, datedudebut DATE DEFAULT NULL, datedufin DATE DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, nomdufichier VARCHAR(255) NOT NULL, typedufichier VARCHAR(255) NOT NULL, chemindufichier VARCHAR(255) NOT NULL, uploadat DATETIME NOT NULL, actualite_id INT DEFAULT NULL, evenement_id INT DEFAULT NULL, INDEX IDX_6A2CA10CA2843073 (actualite_id), INDEX IDX_6A2CA10CFD02F13 (evenement_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE parametre (id INT AUTO_INCREMENT NOT NULL, cle VARCHAR(255) NOT NULL, valeur VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA2843073 FOREIGN KEY (actualite_id) REFERENCES actualite (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA2843073');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CFD02F13');
        $this->addSql('DROP TABLE actualite');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE parametre');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
