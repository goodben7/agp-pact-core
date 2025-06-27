<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250626131417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE prejudice_consequence (id INT AUTO_INCREMENT NOT NULL, prejudice_id VARCHAR(16) NOT NULL, consequence_type_id VARCHAR(16) NOT NULL, INDEX IDX_62FBC2A2F88AD7B9 (prejudice_id), INDEX IDX_62FBC2A22C5A1844 (consequence_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice_consequence ADD CONSTRAINT FK_62FBC2A2F88AD7B9 FOREIGN KEY (prejudice_id) REFERENCES prejudice (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice_consequence ADD CONSTRAINT FK_62FBC2A22C5A1844 FOREIGN KEY (consequence_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1F2C5A1844
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_39465C1F2C5A1844 ON prejudice
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP consequence_type_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice_consequence DROP FOREIGN KEY FK_62FBC2A2F88AD7B9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice_consequence DROP FOREIGN KEY FK_62FBC2A22C5A1844
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE prejudice_consequence
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD consequence_type_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1F2C5A1844 FOREIGN KEY (consequence_type_id) REFERENCES general_parameter (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_39465C1F2C5A1844 ON prejudice (consequence_type_id)
        SQL);
    }
}
