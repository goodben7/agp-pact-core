<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719190513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE company ADD location_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company ADD CONSTRAINT FK_4FBF094F64D218E FOREIGN KEY (location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4FBF094F64D218E ON company (location_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD is_receivable TINYINT(1) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `member` ADD profile_picture VARCHAR(255) DEFAULT NULL, ADD contract_start_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', ADD contract_end_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', ADD job_title VARCHAR(255) DEFAULT NULL, ADD `rank` VARCHAR(100) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F64D218E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4FBF094F64D218E ON company
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company DROP location_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP is_receivable
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `member` DROP profile_picture, DROP contract_start_date, DROP contract_end_date, DROP job_title, DROP `rank`
        SQL);
    }
}
