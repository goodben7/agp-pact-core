<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709193430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE company_road_axis (company_id VARCHAR(16) NOT NULL, road_axis_id VARCHAR(16) NOT NULL, INDEX IDX_A220CCFB979B1AD6 (company_id), INDEX IDX_A220CCFB7D3C2BE7 (road_axis_id), PRIMARY KEY(company_id, road_axis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE complaint_step_assignment (id VARCHAR(16) NOT NULL, complaint_id VARCHAR(16) NOT NULL, workflow_step_id VARCHAR(16) NOT NULL, assigned_company_id VARCHAR(16) NOT NULL, assigned_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_74BBD00AEDAE188E (complaint_id), INDEX IDX_74BBD00A71FE882C (workflow_step_id), INDEX IDX_74BBD00AAF3A79A7 (assigned_company_id), UNIQUE INDEX UNIQ_COMPLAINT_STEP (complaint_id, workflow_step_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_road_axis ADD CONSTRAINT FK_A220CCFB979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_road_axis ADD CONSTRAINT FK_A220CCFB7D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_step_assignment ADD CONSTRAINT FK_74BBD00AEDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_step_assignment ADD CONSTRAINT FK_74BBD00A71FE882C FOREIGN KEY (workflow_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_step_assignment ADD CONSTRAINT FK_74BBD00AAF3A79A7 FOREIGN KEY (assigned_company_id) REFERENCES company (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE company_road_axis DROP FOREIGN KEY FK_A220CCFB979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_road_axis DROP FOREIGN KEY FK_A220CCFB7D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_step_assignment DROP FOREIGN KEY FK_74BBD00AEDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_step_assignment DROP FOREIGN KEY FK_74BBD00A71FE882C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_step_assignment DROP FOREIGN KEY FK_74BBD00AAF3A79A7
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company_road_axis
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complaint_step_assignment
        SQL);
    }
}
