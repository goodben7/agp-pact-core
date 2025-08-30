<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250830161126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD current_assigned_company_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5D59C7B48 FOREIGN KEY (current_assigned_company_id) REFERENCES company (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5F2732B5D59C7B48 ON complaint (current_assigned_company_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5D59C7B48
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5F2732B5D59C7B48 ON complaint
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP current_assigned_company_id
        SQL);
    }
}
