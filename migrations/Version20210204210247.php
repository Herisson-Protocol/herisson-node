<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204210247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE remotebackup (id INT AUTO_INCREMENT NOT NULL, size INT NOT NULL, nb_bookmarks INT NOT NULL, created_at DATETIME NOT NULL, friend_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookmark (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(2048) NOT NULL, hash VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, favicon_url VARCHAR(255) DEFAULT NULL, favicon_image VARCHAR(255) DEFAULT NULL, is_public TINYINT(1) NOT NULL, content_image VARCHAR(255) NOT NULL, error INT NOT NULL, expires_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, type_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friend (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(2048) NOT NULL, alias VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, public_key LONGTEXT NOT NULL, is_active TINYINT(1) NOT NULL, is_youwant TINYINT(1) NOT NULL, is_wantsyou TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE screenshoter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, fonction VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, bookmark_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE remotebackup');
        $this->addSql('DROP TABLE bookmark');
        $this->addSql('DROP TABLE friend');
        $this->addSql('DROP TABLE screenshoter');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE type');
    }
}
