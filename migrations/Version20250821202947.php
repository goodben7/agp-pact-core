<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250821202947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE default_assignment_rule_company (default_assignment_rule_id VARCHAR(16) NOT NULL, company_id VARCHAR(16) NOT NULL, INDEX IDX_ED030BA877354044 (default_assignment_rule_id), INDEX IDX_ED030BA8979B1AD6 (company_id), PRIMARY KEY(default_assignment_rule_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE default_assignment_rule_profile (default_assignment_rule_id VARCHAR(16) NOT NULL, profile_id VARCHAR(16) NOT NULL, INDEX IDX_23EBA8E877354044 (default_assignment_rule_id), INDEX IDX_23EBA8E8CCFA12B8 (profile_id), PRIMARY KEY(default_assignment_rule_id, profile_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_company ADD CONSTRAINT FK_ED030BA877354044 FOREIGN KEY (default_assignment_rule_id) REFERENCES default_assignment_rule (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_company ADD CONSTRAINT FK_ED030BA8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_profile ADD CONSTRAINT FK_23EBA8E877354044 FOREIGN KEY (default_assignment_rule_id) REFERENCES default_assignment_rule (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_profile ADD CONSTRAINT FK_23EBA8E8CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE
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
            DROP INDEX IDX_8588C98C64D218E ON default_assignment_rule
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8588C98C7D3C2BE7 ON default_assignment_rule
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8588C98CAF3A79A7 ON default_assignment_rule
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8588C98CF45B71C9 ON default_assignment_rule
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD location TINYINT(1) DEFAULT NULL, ADD road_axis TINYINT(1) DEFAULT NULL, DROP location_id, DROP road_axis_id, DROP assigned_company_id, DROP assigned_profile_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_company DROP FOREIGN KEY FK_ED030BA877354044
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_company DROP FOREIGN KEY FK_ED030BA8979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_profile DROP FOREIGN KEY FK_23EBA8E877354044
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_profile DROP FOREIGN KEY FK_23EBA8E8CCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE default_assignment_rule_company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE default_assignment_rule_profile
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD location_id VARCHAR(16) DEFAULT NULL, ADD road_axis_id VARCHAR(16) DEFAULT NULL, ADD assigned_company_id VARCHAR(16) DEFAULT NULL, ADD assigned_profile_id VARCHAR(16) DEFAULT NULL, DROP location, DROP road_axis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98C64D218E FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98C7D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98CAF3A79A7 FOREIGN KEY (assigned_company_id) REFERENCES company (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule ADD CONSTRAINT FK_8588C98CF45B71C9 FOREIGN KEY (assigned_profile_id) REFERENCES profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8588C98C64D218E ON default_assignment_rule (location_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8588C98C7D3C2BE7 ON default_assignment_rule (road_axis_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8588C98CAF3A79A7 ON default_assignment_rule (assigned_company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8588C98CF45B71C9 ON default_assignment_rule (assigned_profile_id)
        SQL);
    }
}
