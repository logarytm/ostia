<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200617054133 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE album (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album_artist (album_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', artist_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D322AB301137ABCF (album_id), INDEX IDX_D322AB30B7970CF8 (artist_id), PRIMARY KEY(album_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artist (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album_artist ADD CONSTRAINT FK_D322AB301137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE album_artist ADD CONSTRAINT FK_D322AB30B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE track ADD album_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD genre_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP metadata_title, DROP metadata_artists, DROP metadata_album_artists, DROP metadata_album, DROP metadata_genre, CHANGE duration duration integer DEFAULT NULL, CHANGE metadata_track_no track_no INT DEFAULT NULL');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A61137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A64296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('CREATE INDEX IDX_D6E3F8A61137ABCF ON track (album_id)');
        $this->addSql('CREATE INDEX IDX_D6E3F8A64296D31F ON track (genre_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE album_artist DROP FOREIGN KEY FK_D322AB301137ABCF');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A61137ABCF');
        $this->addSql('ALTER TABLE album_artist DROP FOREIGN KEY FK_D322AB30B7970CF8');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A64296D31F');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE album_artist');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP INDEX IDX_D6E3F8A61137ABCF ON track');
        $this->addSql('DROP INDEX IDX_D6E3F8A64296D31F ON track');
        $this->addSql('ALTER TABLE track ADD metadata_title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD metadata_artists LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', ADD metadata_album_artists VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD metadata_album VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD metadata_genre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP album_id, DROP genre_id, CHANGE duration duration INT DEFAULT NULL, CHANGE track_no metadata_track_no INT DEFAULT NULL');
    }
}
