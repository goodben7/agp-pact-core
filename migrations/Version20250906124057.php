<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250906124057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE default_assignment_rule_general_parameter (default_assignment_rule_id VARCHAR(16) NOT NULL, general_parameter_id VARCHAR(16) NOT NULL, INDEX IDX_6306BCB677354044 (default_assignment_rule_id), INDEX IDX_6306BCB6C16C5256 (general_parameter_id), PRIMARY KEY(default_assignment_rule_id, general_parameter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_general_parameter ADD CONSTRAINT FK_6306BCB677354044 FOREIGN KEY (default_assignment_rule_id) REFERENCES default_assignment_rule (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_general_parameter ADD CONSTRAINT FK_6306BCB6C16C5256 FOREIGN KEY (general_parameter_id) REFERENCES general_parameter (id) ON DELETE CASCADE
        SQL);
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE default_assignment_rule_company (default_assignment_rule_id VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, company_id VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_ED030BA877354044 (default_assignment_rule_id), INDEX IDX_ED030BA8979B1AD6 (company_id), PRIMARY KEY(default_assignment_rule_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE default_assignment_rule_profile (default_assignment_rule_id VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, profile_id VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_23EBA8E877354044 (default_assignment_rule_id), INDEX IDX_23EBA8E8CCFA12B8 (profile_id), PRIMARY KEY(default_assignment_rule_id, profile_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_company ADD CONSTRAINT FK_ED030BA877354044 FOREIGN KEY (default_assignment_rule_id) REFERENCES default_assignment_rule (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_company ADD CONSTRAINT FK_ED030BA8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_profile ADD CONSTRAINT FK_23EBA8E877354044 FOREIGN KEY (default_assignment_rule_id) REFERENCES default_assignment_rule (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_profile ADD CONSTRAINT FK_23EBA8E8CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_general_parameter DROP FOREIGN KEY FK_6306BCB677354044
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE default_assignment_rule_general_parameter DROP FOREIGN KEY FK_6306BCB6C16C5256
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE default_assignment_rule_general_parameter
        SQL);
    }
}
