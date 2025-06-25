<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625030044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD incident_cause_id VARCHAR(16) DEFAULT NULL, ADD consequence_type_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1F9E27466D FOREIGN KEY (incident_cause_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1F2C5A1844 FOREIGN KEY (consequence_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_39465C1F9E27466D ON prejudice (incident_cause_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_39465C1F2C5A1844 ON prejudice (consequence_type_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1F9E27466D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1F2C5A1844
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_39465C1F9E27466D ON prejudice
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_39465C1F2C5A1844 ON prejudice
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP incident_cause_id, DROP consequence_type_id
        SQL);
    }
}
