<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250729102600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE species ADD unit_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species ADD CONSTRAINT FK_A50FF712F8BD700D FOREIGN KEY (unit_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A50FF712F8BD700D ON species (unit_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE species DROP FOREIGN KEY FK_A50FF712F8BD700D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A50FF712F8BD700D ON species
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species DROP unit_id
        SQL);
    }
}
