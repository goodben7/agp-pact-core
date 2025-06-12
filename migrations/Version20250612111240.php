<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612111240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE location CHANGE parent_id parent_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_WORKFLOW_ACTION_NAME ON workflow_action (name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_WORKFLOW_STEP_NAME ON workflow_step (name)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE location CHANGE parent_id parent_id VARCHAR(16) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_WORKFLOW_ACTION_NAME ON workflow_action
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_WORKFLOW_STEP_NAME ON workflow_step
        SQL);
    }
}
