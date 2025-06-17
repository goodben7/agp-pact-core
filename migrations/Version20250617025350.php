<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250617025350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE pap (id VARCHAR(16) NOT NULL, person_type_id VARCHAR(16) NOT NULL, vulnerability_degree_id VARCHAR(16) DEFAULT NULL, province_id VARCHAR(16) DEFAULT NULL, territory_id VARCHAR(16) DEFAULT NULL, village_id VARCHAR(16) DEFAULT NULL, code VARCHAR(120) NOT NULL, full_name VARCHAR(255) NOT NULL, gender VARCHAR(10) DEFAULT NULL, age INT DEFAULT NULL, contact_phone_number VARCHAR(15) DEFAULT NULL, identification_number VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(180) DEFAULT NULL, reference_kilometer_point VARCHAR(255) DEFAULT NULL, orientation VARCHAR(255) DEFAULT NULL, property_type VARCHAR(255) DEFAULT NULL, land_affected_surface DOUBLE PRECISION DEFAULT NULL, land_cu DOUBLE PRECISION DEFAULT NULL, land_added_value DOUBLE PRECISION DEFAULT NULL, building_affected_surface DOUBLE PRECISION DEFAULT NULL, annex_surface DOUBLE PRECISION DEFAULT NULL, building_cu DOUBLE PRECISION DEFAULT NULL, building_added_value DOUBLE PRECISION DEFAULT NULL, commercial_activity_affected VARCHAR(255) DEFAULT NULL, number_of_days_affected_per_week INT DEFAULT NULL, cultivated_affected_surface DOUBLE PRECISION DEFAULT NULL, affected_trees INT DEFAULT NULL, rental_income_loss DOUBLE PRECISION DEFAULT NULL, relocation_assistance DOUBLE PRECISION DEFAULT NULL, vulnerable_person_assistance DOUBLE PRECISION DEFAULT NULL, total_dollar_equivalent DOUBLE PRECISION DEFAULT NULL, site_release_agreement TINYINT(1) NOT NULL, longitude DOUBLE PRECISION DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, category VARCHAR(255) DEFAULT NULL, INDEX IDX_87539A98E7D23F1A (person_type_id), INDEX IDX_87539A982C351FDB (vulnerability_degree_id), INDEX IDX_87539A98E946114A (province_id), INDEX IDX_87539A9873F74AD4 (territory_id), INDEX IDX_87539A985E0D5582 (village_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap ADD CONSTRAINT FK_87539A98E7D23F1A FOREIGN KEY (person_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap ADD CONSTRAINT FK_87539A982C351FDB FOREIGN KEY (vulnerability_degree_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap ADD CONSTRAINT FK_87539A98E946114A FOREIGN KEY (province_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap ADD CONSTRAINT FK_87539A9873F74AD4 FOREIGN KEY (territory_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap ADD CONSTRAINT FK_87539A985E0D5582 FOREIGN KEY (village_id) REFERENCES location (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE pap DROP FOREIGN KEY FK_87539A98E7D23F1A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap DROP FOREIGN KEY FK_87539A982C351FDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap DROP FOREIGN KEY FK_87539A98E946114A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap DROP FOREIGN KEY FK_87539A9873F74AD4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pap DROP FOREIGN KEY FK_87539A985E0D5582
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pap
        SQL);
    }
}
