<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201210615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE match_result ADD tourney_id INT NOT NULL');
        $this->addSql('ALTER TABLE match_result ADD CONSTRAINT FK_B2053812ECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B2053812ECAE3834 ON match_result (tourney_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE match_result DROP CONSTRAINT FK_B2053812ECAE3834');
        $this->addSql('DROP INDEX IDX_B2053812ECAE3834');
        $this->addSql('ALTER TABLE match_result DROP tourney_id');
    }
}
