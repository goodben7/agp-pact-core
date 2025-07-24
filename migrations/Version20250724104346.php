<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724104346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template_profile DROP FOREIGN KEY FK_58EB91CBCCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template_profile DROP FOREIGN KEY FK_58EB91CBD0413CF9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification_template_profile
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template DROP is_sensible
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE notification_template_profile (notification_template_id VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, profile_id VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_58EB91CBD0413CF9 (notification_template_id), INDEX IDX_58EB91CBCCFA12B8 (profile_id), PRIMARY KEY(notification_template_id, profile_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template_profile ADD CONSTRAINT FK_58EB91CBCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template_profile ADD CONSTRAINT FK_58EB91CBD0413CF9 FOREIGN KEY (notification_template_id) REFERENCES notification_template (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_template ADD is_sensible TINYINT(1) DEFAULT NULL
        SQL);
    }
}
