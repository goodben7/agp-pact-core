<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614200005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id VARCHAR(16) NOT NULL, type_id VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, contact_email VARCHAR(180) DEFAULT NULL, contact_phone VARCHAR(15) DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_4FBF094FC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company ADD CONSTRAINT FK_4FBF094FC54C8C93 FOREIGN KEY (type_id) REFERENCES general_parameter (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FC54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
    }
}
