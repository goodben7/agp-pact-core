<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911012736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE import_batch (id VARCHAR(16) NOT NULL, mapping_id VARCHAR(16) NOT NULL, uploaded_by_id VARCHAR(16) NOT NULL, file_path VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, entity_type VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, total_items INT NOT NULL, processed_items INT NOT NULL, successful_items INT NOT NULL, failed_items INT NOT NULL, uploaded_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', completed_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_155EFD1AFABB77CC (mapping_id), INDEX IDX_155EFD1AA2B28FE8 (uploaded_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE import_item (id VARCHAR(16) NOT NULL, batch_id VARCHAR(16) NOT NULL, row_data JSON NOT NULL, status VARCHAR(20) NOT NULL, error_message LONGTEXT DEFAULT NULL, processed_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_F237CB9EF39EBE7A (batch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE import_mapping (id VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, entity_type VARCHAR(100) NOT NULL, mapping_configuration JSON NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE import_batch ADD CONSTRAINT FK_155EFD1AFABB77CC FOREIGN KEY (mapping_id) REFERENCES import_mapping (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE import_batch ADD CONSTRAINT FK_155EFD1AA2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE import_item ADD CONSTRAINT FK_F237CB9EF39EBE7A FOREIGN KEY (batch_id) REFERENCES import_batch (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE import_batch DROP FOREIGN KEY FK_155EFD1AFABB77CC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE import_batch DROP FOREIGN KEY FK_155EFD1AA2B28FE8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE import_item DROP FOREIGN KEY FK_F237CB9EF39EBE7A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE import_batch
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE import_item
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE import_mapping
        SQL);
    }
}
