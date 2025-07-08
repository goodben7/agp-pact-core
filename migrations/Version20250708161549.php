<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708161549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE `member` (id VARCHAR(16) NOT NULL, company_id VARCHAR(16) NOT NULL, display_name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', user_id VARCHAR(16) DEFAULT NULL, INDEX IDX_70E4FA78979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `member`
        SQL);
    }
}
