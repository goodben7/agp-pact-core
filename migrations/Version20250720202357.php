<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250720202357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE affected_species (id VARCHAR(16) NOT NULL, complaint_id VARCHAR(16) NOT NULL, species_type_id VARCHAR(16) NOT NULL, affected_unit_id VARCHAR(16) NOT NULL, asset_type_id VARCHAR(16) NOT NULL, affected_quantity DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_EF862293EDAE188E (complaint_id), INDEX IDX_EF86229367D2FDDC (species_type_id), INDEX IDX_EF862293B234A58C (affected_unit_id), INDEX IDX_EF862293A6A2CDC5 (asset_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE species (id VARCHAR(16) NOT NULL, category_id VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, INDEX IDX_A50FF71212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE species_price (id VARCHAR(16) NOT NULL, species_type_id VARCHAR(16) NOT NULL, road_axis_id VARCHAR(16) NOT NULL, unit_id VARCHAR(16) NOT NULL, currency_id VARCHAR(16) DEFAULT NULL, price_per_unit DOUBLE PRECISION DEFAULT NULL, effective_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', expiration_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_732A6E4567D2FDDC (species_type_id), INDEX IDX_732A6E457D3C2BE7 (road_axis_id), INDEX IDX_732A6E45F8BD700D (unit_id), INDEX IDX_732A6E4538248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF862293EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF86229367D2FDDC FOREIGN KEY (species_type_id) REFERENCES species (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF862293B234A58C FOREIGN KEY (affected_unit_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF862293A6A2CDC5 FOREIGN KEY (asset_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species ADD CONSTRAINT FK_A50FF71212469DE2 FOREIGN KEY (category_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E4567D2FDDC FOREIGN KEY (species_type_id) REFERENCES species (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E457D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E45F8BD700D FOREIGN KEY (unit_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E4538248176 FOREIGN KEY (currency_id) REFERENCES general_parameter (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF862293EDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF86229367D2FDDC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF862293B234A58C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF862293A6A2CDC5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species DROP FOREIGN KEY FK_A50FF71212469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E4567D2FDDC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E457D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E45F8BD700D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E4538248176
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE affected_species
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE species
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE species_price
        SQL);
    }
}
