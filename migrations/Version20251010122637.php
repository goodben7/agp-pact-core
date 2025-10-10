<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251010122637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE payment_history (id VARCHAR(16) NOT NULL, par_id VARCHAR(16) NOT NULL, payment_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', amount VARCHAR(255) NOT NULL, transaction_reference VARCHAR(255) DEFAULT NULL, payment_method VARCHAR(255) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_3EF37EA1468486AA (par_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payment_history ADD CONSTRAINT FK_3EF37EA1468486AA FOREIGN KEY (par_id) REFERENCES par (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE payment_history DROP FOREIGN KEY FK_3EF37EA1468486AA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payment_history
        SQL);
    }
}
