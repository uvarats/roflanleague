<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222190433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tourney_invite_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tourney_invite (id INT NOT NULL, tourney_id INT NOT NULL, slug VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D824059DECAE3834 ON tourney_invite (tourney_id)');
        $this->addSql('ALTER TABLE tourney_invite ADD CONSTRAINT FK_D824059DECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tourney_invite_id_seq CASCADE');
        $this->addSql('ALTER TABLE tourney_invite DROP CONSTRAINT FK_D824059DECAE3834');
        $this->addSql('DROP TABLE tourney_invite');
    }
}
