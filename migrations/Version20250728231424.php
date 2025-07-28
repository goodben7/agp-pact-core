<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728231424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE offender (id VARCHAR(16) NOT NULL, gender_id VARCHAR(16) DEFAULT NULL, complaint_id VARCHAR(16) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, full_name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_39566CE5708A0E0 (gender_id), INDEX IDX_39566CE5EDAE188E (complaint_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offender ADD CONSTRAINT FK_39566CE5708A0E0 FOREIGN KEY (gender_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offender ADD CONSTRAINT FK_39566CE5EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE offender DROP FOREIGN KEY FK_39566CE5708A0E0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offender DROP FOREIGN KEY FK_39566CE5EDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE offender
        SQL);
    }
}
