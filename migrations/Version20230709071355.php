<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230709071355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE match_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE player_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE match_group_entity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_entity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE player_entity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE match_group_entity (id INT NOT NULL, short_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, num_players_required INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E829AD33F8496E51 ON match_group_entity (short_id)');
        $this->addSql('CREATE TABLE match_group_entity_player_entity (match_group_entity_id INT NOT NULL, player_entity_id INT NOT NULL, PRIMARY KEY(match_group_entity_id, player_entity_id))');
        $this->addSql('CREATE INDEX IDX_A5995EE01977C4D9 ON match_group_entity_player_entity (match_group_entity_id)');
        $this->addSql('CREATE INDEX IDX_A5995EE021294864 ON match_group_entity_player_entity (player_entity_id)');
        $this->addSql('CREATE TABLE message_entity (id INT NOT NULL, match_group_id INT DEFAULT NULL, sender VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_390FD967232E03D1 ON message_entity (match_group_id)');
        $this->addSql('CREATE TABLE player_entity (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, auth_token VARCHAR(255) DEFAULT NULL, wants_notifications BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE match_group_entity_player_entity ADD CONSTRAINT FK_A5995EE01977C4D9 FOREIGN KEY (match_group_entity_id) REFERENCES match_group_entity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_group_entity_player_entity ADD CONSTRAINT FK_A5995EE021294864 FOREIGN KEY (player_entity_id) REFERENCES player_entity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_entity ADD CONSTRAINT FK_390FD967232E03D1 FOREIGN KEY (match_group_id) REFERENCES match_group_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_group_player DROP CONSTRAINT fk_7e18933e232e03d1');
        $this->addSql('ALTER TABLE match_group_player DROP CONSTRAINT fk_7e18933e99e6f5df');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f232e03d1');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE match_group_player');
        $this->addSql('DROP TABLE match_group');
        $this->addSql('DROP TABLE message');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE match_group_entity_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_entity_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE player_entity_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE match_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE player_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE player (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, auth_token VARCHAR(255) DEFAULT NULL, wants_notifications BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE match_group_player (match_group_id INT NOT NULL, player_id INT NOT NULL, PRIMARY KEY(match_group_id, player_id))');
        $this->addSql('CREATE INDEX idx_7e18933e99e6f5df ON match_group_player (player_id)');
        $this->addSql('CREATE INDEX idx_7e18933e232e03d1 ON match_group_player (match_group_id)');
        $this->addSql('CREATE TABLE match_group (id INT NOT NULL, short_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, num_players_required INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_d3aa3b64f8496e51 ON match_group (short_id)');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, match_group_id INT DEFAULT NULL, sender VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_b6bd307f232e03d1 ON message (match_group_id)');
        $this->addSql('ALTER TABLE match_group_player ADD CONSTRAINT fk_7e18933e232e03d1 FOREIGN KEY (match_group_id) REFERENCES match_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_group_player ADD CONSTRAINT fk_7e18933e99e6f5df FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f232e03d1 FOREIGN KEY (match_group_id) REFERENCES match_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_group_entity_player_entity DROP CONSTRAINT FK_A5995EE01977C4D9');
        $this->addSql('ALTER TABLE match_group_entity_player_entity DROP CONSTRAINT FK_A5995EE021294864');
        $this->addSql('ALTER TABLE message_entity DROP CONSTRAINT FK_390FD967232E03D1');
        $this->addSql('DROP TABLE match_group_entity');
        $this->addSql('DROP TABLE match_group_entity_player_entity');
        $this->addSql('DROP TABLE message_entity');
        $this->addSql('DROP TABLE player_entity');
    }
}
