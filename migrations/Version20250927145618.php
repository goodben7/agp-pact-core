<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927145618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis_province (road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_75E3F0927D3C2BE7 (road_axis_id), INDEX IDX_75E3F09264D218E (location_id), PRIMARY KEY(road_axis_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis_territory (road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_60982BA27D3C2BE7 (road_axis_id), INDEX IDX_60982BA264D218E (location_id), PRIMARY KEY(road_axis_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis_commune (road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_6EC095487D3C2BE7 (road_axis_id), INDEX IDX_6EC0954864D218E (location_id), PRIMARY KEY(road_axis_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis_quartier (road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_C1D1B2B47D3C2BE7 (road_axis_id), INDEX IDX_C1D1B2B464D218E (location_id), PRIMARY KEY(road_axis_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis_city (road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_F9B5F5F57D3C2BE7 (road_axis_id), INDEX IDX_F9B5F5F564D218E (location_id), PRIMARY KEY(road_axis_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis_secteur (road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_C6761B97D3C2BE7 (road_axis_id), INDEX IDX_C6761B964D218E (location_id), PRIMARY KEY(road_axis_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_province ADD CONSTRAINT FK_75E3F0927D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_province ADD CONSTRAINT FK_75E3F09264D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_territory ADD CONSTRAINT FK_60982BA27D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_territory ADD CONSTRAINT FK_60982BA264D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_commune ADD CONSTRAINT FK_6EC095487D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_commune ADD CONSTRAINT FK_6EC0954864D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_quartier ADD CONSTRAINT FK_C1D1B2B47D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_quartier ADD CONSTRAINT FK_C1D1B2B464D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_city ADD CONSTRAINT FK_F9B5F5F57D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_city ADD CONSTRAINT FK_F9B5F5F564D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_secteur ADD CONSTRAINT FK_C6761B97D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_secteur ADD CONSTRAINT FK_C6761B964D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis DROP FOREIGN KEY FK_913B460B5C3A313A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis DROP FOREIGN KEY FK_913B460BC43C7F1
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_913B460B5C3A313A ON road_axis
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_913B460BC43C7F1 ON road_axis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis DROP start_location_id, DROP end_location_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_province DROP FOREIGN KEY FK_75E3F0927D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_province DROP FOREIGN KEY FK_75E3F09264D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_territory DROP FOREIGN KEY FK_60982BA27D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_territory DROP FOREIGN KEY FK_60982BA264D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_commune DROP FOREIGN KEY FK_6EC095487D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_commune DROP FOREIGN KEY FK_6EC0954864D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_quartier DROP FOREIGN KEY FK_C1D1B2B47D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_quartier DROP FOREIGN KEY FK_C1D1B2B464D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_city DROP FOREIGN KEY FK_F9B5F5F57D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_city DROP FOREIGN KEY FK_F9B5F5F564D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_secteur DROP FOREIGN KEY FK_C6761B97D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_secteur DROP FOREIGN KEY FK_C6761B964D218E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis_province
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis_territory
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis_commune
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis_quartier
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis_city
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis_secteur
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis ADD start_location_id VARCHAR(16) DEFAULT NULL, ADD end_location_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis ADD CONSTRAINT FK_913B460B5C3A313A FOREIGN KEY (start_location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis ADD CONSTRAINT FK_913B460BC43C7F1 FOREIGN KEY (end_location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_913B460B5C3A313A ON road_axis (start_location_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_913B460BC43C7F1 ON road_axis (end_location_id)
        SQL);
    }
}
