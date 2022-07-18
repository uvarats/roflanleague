<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220717180834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE season_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tourney_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE season (id INT NOT NULL, tourney_id INT NOT NULL, name VARCHAR(50) NOT NULL, challonge_id VARCHAR(30) NOT NULL, is_current BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F0E45BA9ECAE3834 ON season (tourney_id)');
        $this->addSql('CREATE TABLE tourney (id INT NOT NULL, name VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9ECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD tourney_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649ECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649ECAE3834 ON "user" (tourney_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE season DROP CONSTRAINT FK_F0E45BA9ECAE3834');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649ECAE3834');
        $this->addSql('DROP SEQUENCE season_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tourney_id_seq CASCADE');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE tourney');
        $this->addSql('DROP INDEX IDX_8D93D649ECAE3834');
        $this->addSql('ALTER TABLE "user" DROP tourney_id');
    }
}
