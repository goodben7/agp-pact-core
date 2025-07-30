<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250730121832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE complaint_incident_causes (complaint_id VARCHAR(16) NOT NULL, prejudice_id VARCHAR(16) NOT NULL, INDEX IDX_A509B57CEDAE188E (complaint_id), INDEX IDX_A509B57CF88AD7B9 (prejudice_id), PRIMARY KEY(complaint_id, prejudice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes ADD CONSTRAINT FK_A509B57CEDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes ADD CONSTRAINT FK_A509B57CF88AD7B9 FOREIGN KEY (prejudice_id) REFERENCES prejudice (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD asset_type_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1FA6A2CDC5 FOREIGN KEY (asset_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_39465C1FA6A2CDC5 ON prejudice (asset_type_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes DROP FOREIGN KEY FK_A509B57CEDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_incident_causes DROP FOREIGN KEY FK_A509B57CF88AD7B9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complaint_incident_causes
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1FA6A2CDC5
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_39465C1FA6A2CDC5 ON prejudice
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP asset_type_id
        SQL);
    }
}
