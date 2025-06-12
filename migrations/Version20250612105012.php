<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612105012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition DROP FOREIGN KEY FK_6A3A796FEF6AA4B6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6A3A796FEF6AA4B6 ON workflow_transition
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition ADD role_required JSON DEFAULT NULL, DROP role_required_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition ADD role_required_id VARCHAR(16) DEFAULT NULL, DROP role_required
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition ADD CONSTRAINT FK_6A3A796FEF6AA4B6 FOREIGN KEY (role_required_id) REFERENCES profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6A3A796FEF6AA4B6 ON workflow_transition (role_required_id)
        SQL);
    }
}
