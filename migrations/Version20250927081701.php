<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927081701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE par ADD is_paid TINYINT(1) DEFAULT NULL, ADD remaining_amount VARCHAR(255) DEFAULT NULL, ADD bank_account_creation_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD bank_account VARCHAR(255) DEFAULT NULL, ADD payment_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE par DROP is_paid, DROP remaining_amount, DROP bank_account_creation_date, DROP bank_account, DROP payment_date
        SQL);
    }
}
