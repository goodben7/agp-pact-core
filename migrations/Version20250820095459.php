<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250820095459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE default_assignment_rule (id VARCHAR(16) NOT NULL, workflow_step_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) DEFAULT NULL, road_axis_id VARCHAR(16) DEFAULT NULL, assigned_company_id VARCHAR(16) DEFAULT NULL, assigned_profile_id VARCHAR(16) DEFAULT NULL, priority INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_8588C98C71FE882C (workflow_step_id), INDEX IDX_8588C98C64D218E (location_id), INDEX IDX_8588C98C7D3C2BE7 (road_axis_id), INDEX IDX_8588C98CAF3A79A7 (assigned_company_id), INDEX IDX_8588C98CF45B71C9 (assigned_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98C71FE882C FOREIGN KEY (workflow_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98C64D218E FOREIGN KEY (location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98C7D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98CAF3A79A7 FOREIGN KEY (assigned_company_id) REFERENCES company (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98CF45B71C9 FOREIGN KEY (assigned_profile_id) REFERENCES profile (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule DROP FOREIGN KEY FK_8588C98C71FE882C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule DROP FOREIGN KEY FK_8588C98C64D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule DROP FOREIGN KEY FK_8588C98C7D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule DROP FOREIGN KEY FK_8588C98CAF3A79A7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule DROP FOREIGN KEY FK_8588C98CF45B71C9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE default_assignment_rule
        SQL);
    }
}
