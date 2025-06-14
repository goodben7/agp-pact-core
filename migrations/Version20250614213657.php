<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614213657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD person_type_id VARCHAR(16) DEFAULT NULL, ADD secteur_id VARCHAR(16) DEFAULT NULL, ADD organization_status_id VARCHAR(16) DEFAULT NULL, ADD legal_personality_id VARCHAR(16) DEFAULT NULL, DROP person_type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D432E7D23F1A FOREIGN KEY (person_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D4329F7E4405 FOREIGN KEY (secteur_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D43253C0DABB FOREIGN KEY (organization_status_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D432EB3FF23 FOREIGN KEY (legal_personality_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A647D432E7D23F1A ON complainant (person_type_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A647D4329F7E4405 ON complainant (secteur_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A647D43253C0DABB ON complainant (organization_status_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A647D432EB3FF23 ON complainant (legal_personality_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D432E7D23F1A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D4329F7E4405
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D43253C0DABB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D432EB3FF23
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A647D432E7D23F1A ON complainant
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A647D4329F7E4405 ON complainant
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A647D43253C0DABB ON complainant
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A647D432EB3FF23 ON complainant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD person_type VARCHAR(10) NOT NULL, DROP person_type_id, DROP secteur_id, DROP organization_status_id, DROP legal_personality_id
        SQL);
    }
}
