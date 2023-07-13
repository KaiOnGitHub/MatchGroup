<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230707164711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE match_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE player_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE match_group (id INT NOT NULL, short_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, num_players_required INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D3AA3B64F8496E51 ON match_group (short_id)');
        $this->addSql('CREATE TABLE match_group_player (match_group_id INT NOT NULL, player_id INT NOT NULL, PRIMARY KEY(match_group_id, player_id))');
        $this->addSql('CREATE INDEX IDX_7E18933E232E03D1 ON match_group_player (match_group_id)');
        $this->addSql('CREATE INDEX IDX_7E18933E99E6F5DF ON match_group_player (player_id)');
        $this->addSql('CREATE TABLE player (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, auth_token VARCHAR(255) DEFAULT NULL, wants_notifications BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE match_group_player ADD CONSTRAINT FK_7E18933E232E03D1 FOREIGN KEY (match_group_id) REFERENCES match_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE match_group_player ADD CONSTRAINT FK_7E18933E99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE match_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE player_id_seq CASCADE');
        $this->addSql('ALTER TABLE match_group_player DROP CONSTRAINT FK_7E18933E232E03D1');
        $this->addSql('ALTER TABLE match_group_player DROP CONSTRAINT FK_7E18933E99E6F5DF');
        $this->addSql('DROP TABLE match_group');
        $this->addSql('DROP TABLE match_group_player');
        $this->addSql('DROP TABLE player');
    }
}
