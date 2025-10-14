<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014134247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE task (id VARCHAR(16) NOT NULL, type VARCHAR(30) NOT NULL, method VARCHAR(15) NOT NULL, status VARCHAR(1) NOT NULL, data JSON NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', created_by VARCHAR(16) NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', updated_by VARCHAR(16) DEFAULT NULL, synchronized_by VARCHAR(16) DEFAULT NULL, synchronized_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', message VARCHAR(255) DEFAULT NULL, data1 VARCHAR(255) DEFAULT NULL, data2 VARCHAR(255) DEFAULT NULL, data3 VARCHAR(255) DEFAULT NULL, data4 VARCHAR(255) DEFAULT NULL, data5 VARCHAR(255) DEFAULT NULL, data6 VARCHAR(255) DEFAULT NULL, external_reference_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE task
        SQL);
    }
}
