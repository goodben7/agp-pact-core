<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723221102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B59E27466D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5F2732B59E27466D ON complaint
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP incident_cause_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD incident_cause_id VARCHAR(16) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B59E27466D FOREIGN KEY (incident_cause_id) REFERENCES general_parameter (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5F2732B59E27466D ON complaint (incident_cause_id)
        SQL);
    }
}
