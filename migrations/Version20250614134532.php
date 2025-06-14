<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614134532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant CHANGE province_id province_id VARCHAR(16) DEFAULT NULL, CHANGE territory_id territory_id VARCHAR(16) DEFAULT NULL, CHANGE commune_id commune_id VARCHAR(16) DEFAULT NULL, CHANGE quartier_id quartier_id VARCHAR(16) DEFAULT NULL, CHANGE city_id city_id VARCHAR(16) DEFAULT NULL, CHANGE village_id village_id VARCHAR(16) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant CHANGE province_id province_id VARCHAR(16) NOT NULL, CHANGE territory_id territory_id VARCHAR(16) NOT NULL, CHANGE commune_id commune_id VARCHAR(16) NOT NULL, CHANGE quartier_id quartier_id VARCHAR(16) NOT NULL, CHANGE city_id city_id VARCHAR(16) NOT NULL, CHANGE village_id village_id VARCHAR(16) NOT NULL
        SQL);
    }
}
