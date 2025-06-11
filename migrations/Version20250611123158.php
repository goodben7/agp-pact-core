<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611123158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE workflow_step_uiconfiguration (id INT AUTO_INCREMENT NOT NULL, workflow_step_id VARCHAR(16) NOT NULL, main_component_key VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, display_fields JSON DEFAULT NULL, input_fields JSON DEFAULT NULL, custom_widgets JSON DEFAULT NULL, UNIQUE INDEX UNIQ_9EBEAAFB71FE882C (workflow_step_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_step_uiconfiguration ADD CONSTRAINT FK_9EBEAAFB71FE882C FOREIGN KEY (workflow_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD incident_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_step_uiconfiguration DROP FOREIGN KEY FK_9EBEAAFB71FE882C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE workflow_step_uiconfiguration
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP incident_date
        SQL);
    }
}
