<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250622001950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD involved_company_id VARCHAR(16) DEFAULT NULL, ADD proposed_resolution_description VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B557B3F61D FOREIGN KEY (involved_company_id) REFERENCES company (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5F2732B557B3F61D ON complaint (involved_company_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B557B3F61D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5F2732B557B3F61D ON complaint
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP involved_company_id, DROP proposed_resolution_description
        SQL);
    }
}
