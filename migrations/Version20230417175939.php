<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230417175939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_rating_update_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_rating_update (id INT NOT NULL, rating_id INT NOT NULL, tourney_id INT DEFAULT NULL, change INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5959F2A0A32EFC6 ON user_rating_update (rating_id)');
        $this->addSql('CREATE INDEX IDX_5959F2A0ECAE3834 ON user_rating_update (tourney_id)');
        $this->addSql('ALTER TABLE user_rating_update ADD CONSTRAINT FK_5959F2A0A32EFC6 FOREIGN KEY (rating_id) REFERENCES user_rating (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_rating_update ADD CONSTRAINT FK_5959F2A0ECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_rating_update_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_rating_update DROP CONSTRAINT FK_5959F2A0A32EFC6');
        $this->addSql('ALTER TABLE user_rating_update DROP CONSTRAINT FK_5959F2A0ECAE3834');
        $this->addSql('DROP TABLE user_rating_update');
    }
}
