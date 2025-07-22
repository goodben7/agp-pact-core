<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722212544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE complaint_incident_causes (complaint_id VARCHAR(16) NOT NULL, general_parameter_id VARCHAR(16) NOT NULL, INDEX IDX_A509B57CEDAE188E (complaint_id), INDEX IDX_A509B57CC16C5256 (general_parameter_id), PRIMARY KEY(complaint_id, general_parameter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes ADD CONSTRAINT FK_A509B57CEDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes ADD CONSTRAINT FK_A509B57CC16C5256 FOREIGN KEY (general_parameter_id) REFERENCES general_parameter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B59E27466D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5F2732B59E27466D ON complaint
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP incident_cause_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes DROP FOREIGN KEY FK_A509B57CEDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes DROP FOREIGN KEY FK_A509B57CC16C5256
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complaint_incident_causes
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD incident_cause_id VARCHAR(16) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B59E27466D FOREIGN KEY (incident_cause_id) REFERENCES general_parameter (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5F2732B59E27466D ON complaint (incident_cause_id)
        SQL);
    }
}
