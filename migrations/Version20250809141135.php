<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250809141135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE company_location (company_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_46099CA6979B1AD6 (company_id), INDEX IDX_46099CA664D218E (location_id), PRIMARY KEY(company_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_location ADD CONSTRAINT FK_46099CA6979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_location ADD CONSTRAINT FK_46099CA664D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F64D218E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4FBF094F64D218E ON company
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company DROP location_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE company_location DROP FOREIGN KEY FK_46099CA6979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company_location DROP FOREIGN KEY FK_46099CA664D218E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company_location
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company ADD location_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company ADD CONSTRAINT FK_4FBF094F64D218E FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4FBF094F64D218E ON company (location_id)
        SQL);
    }
}
