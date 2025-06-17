<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250617054324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE generated_report (id VARCHAR(16) NOT NULL, template_id VARCHAR(16) NOT NULL, requested_by_id VARCHAR(16) NOT NULL, requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', status VARCHAR(50) NOT NULL, file_path VARCHAR(255) DEFAULT NULL, completed_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', error_message LONGTEXT DEFAULT NULL, filters_applied JSON DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, INDEX IDX_498C20895DA0FB8 (template_id), INDEX IDX_498C20894DA1E751 (requested_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE report_template (id VARCHAR(16) NOT NULL, report_type_id VARCHAR(16) NOT NULL, format_id VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, template_path_or_content VARCHAR(255) DEFAULT NULL, available_filters LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', default_filter_values JSON DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_970086FEA5D5F193 (report_type_id), INDEX IDX_970086FED629F605 (format_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE generated_report ADD CONSTRAINT FK_498C20895DA0FB8 FOREIGN KEY (template_id) REFERENCES report_template (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE generated_report ADD CONSTRAINT FK_498C20894DA1E751 FOREIGN KEY (requested_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report_template ADD CONSTRAINT FK_970086FEA5D5F193 FOREIGN KEY (report_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report_template ADD CONSTRAINT FK_970086FED629F605 FOREIGN KEY (format_id) REFERENCES general_parameter (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE generated_report DROP FOREIGN KEY FK_498C20895DA0FB8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE generated_report DROP FOREIGN KEY FK_498C20894DA1E751
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report_template DROP FOREIGN KEY FK_970086FEA5D5F193
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report_template DROP FOREIGN KEY FK_970086FED629F605
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE generated_report
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE report_template
        SQL);
    }
}
