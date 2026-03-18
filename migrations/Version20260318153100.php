<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260318153100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE par_v2 ADD validated_by VARCHAR(16) DEFAULT NULL, ADD status VARCHAR(1) DEFAULT 'P' NOT NULL, ADD validated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE par_v2 ADD CONSTRAINT FK_9D67CF5F54EF1C FOREIGN KEY (validated_by) REFERENCES `user` (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9D67CF5F54EF1C ON par_v2 (validated_by)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payment_history ADD par_v2_id VARCHAR(16) DEFAULT NULL, CHANGE par_id par_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payment_history ADD CONSTRAINT FK_3EF37EA139743519 FOREIGN KEY (par_v2_id) REFERENCES par_v2 (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3EF37EA139743519 ON payment_history (par_v2_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE par_v2 DROP FOREIGN KEY FK_9D67CF5F54EF1C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9D67CF5F54EF1C ON par_v2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE par_v2 DROP validated_by, DROP status, DROP validated_at, CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payment_history DROP FOREIGN KEY FK_3EF37EA139743519
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3EF37EA139743519 ON payment_history
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payment_history DROP par_v2_id, CHANGE par_id par_id VARCHAR(16) NOT NULL
        SQL);
    }
}
