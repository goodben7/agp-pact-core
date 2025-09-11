<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911153040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE par (id VARCHAR(16) NOT NULL, type VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, fullname VARCHAR(255) DEFAULT NULL, sexe VARCHAR(16) DEFAULT NULL, age INT DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, deceased_name_or_description_vault LONGTEXT DEFAULT NULL, place_of_birth_deceased VARCHAR(16) DEFAULT NULL, date_of_birth_deceased DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', deceased_residence VARCHAR(16) DEFAULT NULL, spouse_name VARCHAR(255) DEFAULT NULL, measures VARCHAR(255) DEFAULT NULL, identification_number VARCHAR(255) DEFAULT NULL, former_pap TINYINT(1) DEFAULT NULL, kilometer_point VARCHAR(255) DEFAULT NULL, category VARCHAR(255) DEFAULT NULL, type_liability VARCHAR(255) DEFAULT NULL, province VARCHAR(16) DEFAULT NULL, territory VARCHAR(16) DEFAULT NULL, village VARCHAR(16) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, latitude VARCHAR(255) DEFAULT NULL, orientation VARCHAR(255) DEFAULT NULL, vulnerability TINYINT(1) DEFAULT NULL, vulnerability_type VARCHAR(16) DEFAULT NULL, tenant_monthly_rent VARCHAR(255) DEFAULT NULL, lessor_name VARCHAR(255) DEFAULT NULL, total_rent VARCHAR(255) DEFAULT NULL, total_loss_employment_income VARCHAR(255) DEFAULT NULL, total_loss_business_income VARCHAR(255) DEFAULT NULL, reference_coordinates VARCHAR(255) DEFAULT NULL, length VARCHAR(255) DEFAULT NULL, wide VARCHAR(255) DEFAULT NULL, area_allocated_square_meters VARCHAR(255) DEFAULT NULL, cu_per_square_meter VARCHAR(255) DEFAULT NULL, capital_gain VARCHAR(255) DEFAULT NULL, total_property_usd VARCHAR(255) DEFAULT NULL, total_batis_usd VARCHAR(255) DEFAULT NULL, commercial_activity VARCHAR(255) DEFAULT NULL, number_working_days_per_week INT DEFAULT NULL, average_daily_income VARCHAR(255) DEFAULT NULL, monthly_income VARCHAR(255) DEFAULT NULL, total_compensation_three_months VARCHAR(255) DEFAULT NULL, affected_cultivated_area VARCHAR(255) DEFAULT NULL, equivalent_usd VARCHAR(255) DEFAULT NULL, tree LONGTEXT DEFAULT NULL, total_farm_income VARCHAR(255) DEFAULT NULL, loss_rental_income VARCHAR(255) DEFAULT NULL, moving_assistance VARCHAR(255) DEFAULT NULL, assistance_vulnerable_persons VARCHAR(255) DEFAULT NULL, rental_guarantee_assistance VARCHAR(255) DEFAULT NULL, notice_agreement_vacating_premises TINYINT(1) DEFAULT NULL, total_general VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE par
        SQL);
    }
}
