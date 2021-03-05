<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301212021 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE local_backup (id INT AUTO_INCREMENT NOT NULL, size INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, friend_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE remote_backup (id INT AUTO_INCREMENT NOT NULL, size INT NOT NULL, nb_bookmarks INT NOT NULL, created_at DATETIME NOT NULL, friend_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE backup');
        $this->addSql('DROP TABLE localbackup');
        $this->addSql('DROP TABLE remotebackup');
        $this->addSql('DROP INDEX content_image ON bookmark');
        $this->addSql('DROP INDEX dirsize ON bookmark');
        $this->addSql('DROP INDEX url ON bookmark');
        $this->addSql('DROP INDEX created_at ON bookmark');
        $this->addSql('DROP INDEX title ON bookmark');
        $this->addSql('DROP INDEX favicon_url ON bookmark');
        $this->addSql('DROP INDEX updated_at ON bookmark');
        $this->addSql('DROP INDEX description ON bookmark');
        $this->addSql('ALTER TABLE bookmark DROP is_binary, DROP content_type, DROP dirsize, CHANGE url url VARCHAR(2048) NOT NULL, CHANGE hash hash VARCHAR(255) NOT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE favicon_image favicon_image VARCHAR(255) DEFAULT NULL, CHANGE is_public is_public TINYINT(1) NOT NULL, CHANGE content_image content_image VARCHAR(255) NOT NULL, CHANGE error error INT NOT NULL, CHANGE expires_at expires_at DATETIME NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('DROP INDEX is_youwant ON friend');
        $this->addSql('DROP INDEX email ON friend');
        $this->addSql('DROP INDEX is_wantsyou ON friend');
        $this->addSql('DROP INDEX name ON friend');
        $this->addSql('DROP INDEX is_active ON friend');
        $this->addSql('DROP INDEX url ON friend');
        $this->addSql('ALTER TABLE friend ADD is_validated_by_us TINYINT(1) NOT NULL, ADD is_validated_by_him TINYINT(1) NOT NULL, CHANGE url url VARCHAR(2048) NOT NULL, CHANGE alias alias VARCHAR(255) NOT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE public_key public_key LONGTEXT NOT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE is_youwant is_youwant TINYINT(1) NOT NULL, CHANGE is_wantsyou is_wantsyou TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX name ON `option`');
        $this->addSql('ALTER TABLE `option` CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE value value VARCHAR(8096) DEFAULT NULL');
        $this->addSql('DROP INDEX name ON screenshoter');
        $this->addSql('ALTER TABLE screenshoter CHANGE name name VARCHAR(255) NOT NULL, CHANGE fonction fonction VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX name ON tag');
        $this->addSql('DROP INDEX bookmark_id ON tag');
        $this->addSql('ALTER TABLE tag CHANGE name name VARCHAR(255) NOT NULL, CHANGE bookmark_id bookmark_id INT NOT NULL');
        $this->addSql('DROP INDEX name ON type');
        $this->addSql('ALTER TABLE type CHANGE name name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE backup (id INT AUTO_INCREMENT NOT NULL, size INT NOT NULL, nb_bookmarks INT NOT NULL, created_at DATETIME NOT NULL, friend_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE localbackup (id INT AUTO_INCREMENT NOT NULL, size INT DEFAULT NULL, filename VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, created_at DATETIME DEFAULT NULL, friend_id INT NOT NULL, INDEX friend_id (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE remotebackup (id INT AUTO_INCREMENT NOT NULL, size INT DEFAULT NULL, nb_bookmarks INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, friend_id INT NOT NULL, INDEX friend_id (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE local_backup');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE remote_backup');
        $this->addSql('ALTER TABLE bookmark ADD is_binary TINYINT(1) DEFAULT \'0\', ADD content_type VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, ADD dirsize INT DEFAULT NULL, CHANGE url url VARCHAR(1024) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE hash hash VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE title title VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE favicon_image favicon_image TEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE is_public is_public TINYINT(1) DEFAULT \'1\', CHANGE content_image content_image VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE error error TINYINT(1) DEFAULT \'0\', CHANGE expires_at expires_at DATETIME DEFAULT \'2038-12-31 00:00:00\', CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX content_image ON bookmark (content_image)');
        $this->addSql('CREATE INDEX dirsize ON bookmark (dirsize)');
        $this->addSql('CREATE INDEX url ON bookmark (url(1000))');
        $this->addSql('CREATE INDEX created_at ON bookmark (created_at)');
        $this->addSql('CREATE INDEX title ON bookmark (title)');
        $this->addSql('CREATE INDEX favicon_url ON bookmark (favicon_url)');
        $this->addSql('CREATE INDEX updated_at ON bookmark (updated_at)');
        $this->addSql('CREATE INDEX description ON bookmark (description(1000))');
        $this->addSql('ALTER TABLE friend DROP is_validated_by_us, DROP is_validated_by_him, CHANGE url url VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE alias alias VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE name name VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE email email VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE public_key public_key TEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE is_active is_active TINYINT(1) DEFAULT \'0\', CHANGE is_youwant is_youwant TINYINT(1) DEFAULT \'0\', CHANGE is_wantsyou is_wantsyou TINYINT(1) DEFAULT \'0\'');
        $this->addSql('CREATE INDEX is_youwant ON friend (is_youwant)');
        $this->addSql('CREATE INDEX email ON friend (email)');
        $this->addSql('CREATE INDEX is_wantsyou ON friend (is_wantsyou)');
        $this->addSql('CREATE INDEX name ON friend (name)');
        $this->addSql('CREATE INDEX is_active ON friend (is_active)');
        $this->addSql('CREATE INDEX url ON friend (url)');
        $this->addSql('ALTER TABLE `option` CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE name name VARCHAR(191) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE value value LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX name ON `option` (name)');
        $this->addSql('ALTER TABLE screenshoter CHANGE name name VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE fonction fonction VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('CREATE INDEX name ON screenshoter (name)');
        $this->addSql('ALTER TABLE tag CHANGE name name VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE bookmark_id bookmark_id VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('CREATE INDEX name ON tag (name)');
        $this->addSql('CREATE INDEX bookmark_id ON tag (bookmark_id)');
        $this->addSql('ALTER TABLE type CHANGE name name VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('CREATE INDEX name ON type (name)');
    }
}
