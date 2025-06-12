<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612125238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D432E7D23F1A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A647D432E7D23F1A ON complainant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD person_type VARCHAR(10) NOT NULL, DROP person_type_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD person_type_id VARCHAR(16) NOT NULL, DROP person_type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D432E7D23F1A FOREIGN KEY (person_type_id) REFERENCES general_parameter (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A647D432E7D23F1A ON complainant (person_type_id)
        SQL);
    }
}
