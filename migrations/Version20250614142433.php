<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614142433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD user_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USER ON complainant (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE email email VARCHAR(180) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_IDENTIFIER_USER ON complainant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` CHANGE email email VARCHAR(180) NOT NULL
        SQL);
    }
}
