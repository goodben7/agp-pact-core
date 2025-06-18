<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618134632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE prejudice (id VARCHAR(16) NOT NULL, category_id VARCHAR(16) NOT NULL, complaint_type_id VARCHAR(16) NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_39465C1F12469DE2 (category_id), INDEX IDX_39465C1FB0046CC2 (complaint_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1F12469DE2 FOREIGN KEY (category_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice ADD CONSTRAINT FK_39465C1FB0046CC2 FOREIGN KEY (complaint_type_id) REFERENCES general_parameter (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1F12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prejudice DROP FOREIGN KEY FK_39465C1FB0046CC2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE prejudice
        SQL);
    }
}
