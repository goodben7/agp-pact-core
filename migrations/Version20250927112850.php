<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927112850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE par ADD road_axis VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP category_id, DROP complaint_type_id, DROP incident_cause_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE par DROP road_axis
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD category_id VARCHAR(16) NOT NULL, ADD complaint_type_id VARCHAR(16) NOT NULL, ADD incident_cause_id VARCHAR(16) DEFAULT NULL
        SQL);
    }
}
