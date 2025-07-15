<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715002731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD closure_reason_id VARCHAR(16) DEFAULT NULL, DROP closure_reason
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B54ECF85E7 FOREIGN KEY (closure_reason_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5F2732B54ECF85E7 ON complaint (closure_reason_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B54ECF85E7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5F2732B54ECF85E7 ON complaint
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD closure_reason LONGTEXT DEFAULT NULL, DROP closure_reason_id
        SQL);
    }
}
