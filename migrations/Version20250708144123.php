<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708144123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD closed TINYINT(1) NOT NULL, ADD satisfaction_comments LONGTEXT DEFAULT NULL, ADD closure_comments LONGTEXT NOT NULL, ADD execution_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD estimated_cost NUMERIC(10, 2) DEFAULT NULL, ADD proposed_resolution_end_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD proposed_resolution_start_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP closed, DROP satisfaction_comments, DROP closure_comments, DROP execution_date, DROP estimated_cost, DROP proposed_resolution_end_date, DROP proposed_resolution_start_date
        SQL);
    }
}
