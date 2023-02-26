<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216200606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tourney ADD discipline_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tourney ADD CONSTRAINT FK_FFF72131A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FFF72131A5522701 ON tourney (discipline_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tourney DROP CONSTRAINT FK_FFF72131A5522701');
        $this->addSql('DROP INDEX IDX_FFF72131A5522701');
        $this->addSql('ALTER TABLE tourney DROP discipline_id');
    }
}
