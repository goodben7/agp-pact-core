<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724122658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template ADD recipient_selectors JSON NOT NULL, CHANGE content content LONGTEXT NOT NULL, CHANGE is_sensitive is_sensitive TINYINT(1) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template DROP recipient_selectors, CHANGE content content LONGTEXT DEFAULT NULL, CHANGE is_sensitive is_sensitive TINYINT(1) NOT NULL
        SQL);
    }
}
