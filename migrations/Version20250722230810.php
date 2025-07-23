<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722230810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cause (id VARCHAR(16) NOT NULL, asset_type_id VARCHAR(16) NOT NULL, value VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, INDEX IDX_F0DA7FBFA6A2CDC5 (asset_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE complaint_incident_causes (complaint_id VARCHAR(16) NOT NULL, cause_id VARCHAR(16) NOT NULL, INDEX IDX_A509B57CEDAE188E (complaint_id), INDEX IDX_A509B57C66E2221E (cause_id), PRIMARY KEY(complaint_id, cause_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cause ADD CONSTRAINT FK_F0DA7FBFA6A2CDC5 FOREIGN KEY (asset_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes ADD CONSTRAINT FK_A509B57CEDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes ADD CONSTRAINT FK_A509B57C66E2221E FOREIGN KEY (cause_id) REFERENCES cause (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1F9E27466D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1F9E27466D FOREIGN KEY (incident_cause_id) REFERENCES cause (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1F9E27466D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cause DROP FOREIGN KEY FK_F0DA7FBFA6A2CDC5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes DROP FOREIGN KEY FK_A509B57CEDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes DROP FOREIGN KEY FK_A509B57C66E2221E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cause
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complaint_incident_causes
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1F9E27466D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1F9E27466D FOREIGN KEY (incident_cause_id) REFERENCES general_parameter (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
