<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250617125309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE victim ADD family_relationship_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim ADD CONSTRAINT FK_5CC0F29B71C1A7 FOREIGN KEY (family_relationship_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5CC0F29B71C1A7 ON victim (family_relationship_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE victim DROP FOREIGN KEY FK_5CC0F29B71C1A7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5CC0F29B71C1A7 ON victim
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim DROP family_relationship_id
        SQL);
    }
}
