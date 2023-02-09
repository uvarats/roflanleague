<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230209184305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE badge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE discipline_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE match_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tourney_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE badge (id INT NOT NULL, text VARCHAR(150) NOT NULL, hex_code VARCHAR(9) NOT NULL, name VARCHAR(25) NOT NULL, priority SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE badge_user (badge_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(badge_id, user_id))');
        $this->addSql('CREATE INDEX IDX_299D3A50F7A2C2FC ON badge_user (badge_id)');
        $this->addSql('CREATE INDEX IDX_299D3A50A76ED395 ON badge_user (user_id)');
        $this->addSql('CREATE TABLE discipline (id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE match_result (id INT NOT NULL, home_player_id INT DEFAULT NULL, away_player_id INT DEFAULT NULL, tourney_id INT NOT NULL, result VARCHAR(15) NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, challonge_match_id INT NOT NULL, home_player_odds DOUBLE PRECISION NOT NULL, away_player_odds DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B2053812E7328C9B ON match_result (home_player_id)');
        $this->addSql('CREATE INDEX IDX_B20538126861DE1 ON match_result (away_player_id)');
        $this->addSql('CREATE INDEX IDX_B2053812ECAE3834 ON match_result (tourney_id)');
        $this->addSql('COMMENT ON COLUMN match_result.finished_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tourney (id INT NOT NULL, name VARCHAR(30) NOT NULL, impact_coefficient DOUBLE PRECISION NOT NULL, challonge_url VARCHAR(255) NOT NULL, state VARCHAR(75) DEFAULT \'new\' NOT NULL, is_ranked BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FFF721315E237E06 ON tourney (name)');
        $this->addSql('CREATE TABLE tourney_user (tourney_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(tourney_id, user_id))');
        $this->addSql('CREATE INDEX IDX_77D66718ECAE3834 ON tourney_user (tourney_id)');
        $this->addSql('CREATE INDEX IDX_77D66718A76ED395 ON tourney_user (user_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(24) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(100) NOT NULL, is_verified BOOLEAN NOT NULL, is_banned BOOLEAN DEFAULT false NOT NULL, register_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'2022-03-13 18:22:35\' NOT NULL, rating INT DEFAULT 100 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE user_rating (id INT NOT NULL, participant_id INT NOT NULL, discipline_id INT DEFAULT NULL, value INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BDDB3D1F9D1C3019 ON user_rating (participant_id)');
        $this->addSql('CREATE INDEX IDX_BDDB3D1FA5522701 ON user_rating (discipline_id)');
        $this->addSql('COMMENT ON COLUMN user_rating.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_rating.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE badge_user ADD CONSTRAINT FK_299D3A50F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE badge_user ADD CONSTRAINT FK_299D3A50A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_result ADD CONSTRAINT FK_B2053812E7328C9B FOREIGN KEY (home_player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_result ADD CONSTRAINT FK_B20538126861DE1 FOREIGN KEY (away_player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_result ADD CONSTRAINT FK_B2053812ECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tourney_user ADD CONSTRAINT FK_77D66718ECAE3834 FOREIGN KEY (tourney_id) REFERENCES tourney (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tourney_user ADD CONSTRAINT FK_77D66718A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_rating ADD CONSTRAINT FK_BDDB3D1F9D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_rating ADD CONSTRAINT FK_BDDB3D1FA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE badge_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE discipline_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE match_result_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tourney_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE user_rating_id_seq CASCADE');
        $this->addSql('ALTER TABLE badge_user DROP CONSTRAINT FK_299D3A50F7A2C2FC');
        $this->addSql('ALTER TABLE badge_user DROP CONSTRAINT FK_299D3A50A76ED395');
        $this->addSql('ALTER TABLE match_result DROP CONSTRAINT FK_B2053812E7328C9B');
        $this->addSql('ALTER TABLE match_result DROP CONSTRAINT FK_B20538126861DE1');
        $this->addSql('ALTER TABLE match_result DROP CONSTRAINT FK_B2053812ECAE3834');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE tourney_user DROP CONSTRAINT FK_77D66718ECAE3834');
        $this->addSql('ALTER TABLE tourney_user DROP CONSTRAINT FK_77D66718A76ED395');
        $this->addSql('ALTER TABLE user_rating DROP CONSTRAINT FK_BDDB3D1F9D1C3019');
        $this->addSql('ALTER TABLE user_rating DROP CONSTRAINT FK_BDDB3D1FA5522701');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE badge_user');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE match_result');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE tourney');
        $this->addSql('DROP TABLE tourney_user');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_rating');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
