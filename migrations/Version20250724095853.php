<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724095853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template ADD profile_id VARCHAR(16) DEFAULT NULL, ADD is_sensitive TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template ADD CONSTRAINT FK_C2702726CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C2702726CCFA12B8 ON notification_template (profile_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template DROP FOREIGN KEY FK_C2702726CCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C2702726CCFA12B8 ON notification_template
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template DROP profile_id, DROP is_sensitive
        SQL);
    }
}
