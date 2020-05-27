<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527044147 extends AbstractMigration
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
        $this->addSql('ALTER TABLE track_file ADD metadata_title VARCHAR(255) DEFAULT NULL, ADD metadata_artists LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD metadata_album VARCHAR(255) DEFAULT NULL, ADD metadata_album_artists VARCHAR(255) DEFAULT NULL, ADD metadata_track_no INT DEFAULT NULL, ADD metadata_genre VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track_file DROP metadata_title, DROP metadata_artists, DROP metadata_album, DROP metadata_album_artists, DROP metadata_track_no, DROP metadata_genre');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
