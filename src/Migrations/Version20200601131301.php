<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601131301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE track_upload CHANGE duration duration integer DEFAULT NULL, CHANGE metadata_title metadata_title VARCHAR(255) DEFAULT NULL, CHANGE metadata_artists metadata_artists LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE metadata_album_artists metadata_album_artists VARCHAR(255) DEFAULT NULL, CHANGE metadata_album metadata_album VARCHAR(255) DEFAULT NULL, CHANGE metadata_track_no metadata_track_no INT DEFAULT NULL, CHANGE metadata_genre metadata_genre VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE track ADD `position` INT NOT NULL, CHANGE duration duration integer NOT NULL, CHANGE metadata_title metadata_title VARCHAR(255) DEFAULT NULL, CHANGE metadata_artists metadata_artists LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE metadata_album_artists metadata_album_artists VARCHAR(255) DEFAULT NULL, CHANGE metadata_album metadata_album VARCHAR(255) DEFAULT NULL, CHANGE metadata_track_no metadata_track_no INT DEFAULT NULL, CHANGE metadata_genre metadata_genre VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track DROP `position`, CHANGE duration duration INT NOT NULL, CHANGE metadata_title metadata_title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE metadata_artists metadata_artists LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE metadata_album_artists metadata_album_artists VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE metadata_album metadata_album VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE metadata_track_no metadata_track_no INT DEFAULT NULL, CHANGE metadata_genre metadata_genre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE track_upload CHANGE duration duration INT DEFAULT NULL, CHANGE metadata_title metadata_title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE metadata_artists metadata_artists LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE metadata_album_artists metadata_album_artists VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE metadata_album metadata_album VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE metadata_track_no metadata_track_no INT DEFAULT NULL, CHANGE metadata_genre metadata_genre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
