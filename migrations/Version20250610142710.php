<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610142710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE affected_species (id VARCHAR(16) NOT NULL, complaint_id VARCHAR(16) NOT NULL, species_type_id VARCHAR(16) NOT NULL, affected_unit_id VARCHAR(16) NOT NULL, asset_type_id VARCHAR(16) NOT NULL, affected_quantity DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_EF862293EDAE188E (complaint_id), INDEX IDX_EF86229367D2FDDC (species_type_id), INDEX IDX_EF862293B234A58C (affected_unit_id), INDEX IDX_EF862293A6A2CDC5 (asset_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE attached_file (id VARCHAR(16) NOT NULL, complaint_id VARCHAR(16) NOT NULL, file_type_id VARCHAR(16) NOT NULL, workflow_step_id VARCHAR(16) DEFAULT NULL, uploaded_by_id VARCHAR(16) NOT NULL, file_name VARCHAR(255) NOT NULL, file_path VARCHAR(255) NOT NULL, file_size INT NOT NULL, mime_type VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_B010289AEDAE188E (complaint_id), INDEX IDX_B010289A9E2A35A8 (file_type_id), INDEX IDX_B010289A71FE882C (workflow_step_id), INDEX IDX_B010289AA2B28FE8 (uploaded_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE complainant (id VARCHAR(16) NOT NULL, person_type_id VARCHAR(16) NOT NULL, province_id VARCHAR(16) NOT NULL, territory_id VARCHAR(16) NOT NULL, commune_id VARCHAR(16) NOT NULL, quartier_id VARCHAR(16) NOT NULL, city_id VARCHAR(16) NOT NULL, village_id VARCHAR(16) NOT NULL, last_name VARCHAR(120) NOT NULL, first_name VARCHAR(120) NOT NULL, middle_name VARCHAR(120) DEFAULT NULL, contact_phone VARCHAR(14) NOT NULL, contact_email VARCHAR(180) DEFAULT NULL, address LONGTEXT DEFAULT NULL, INDEX IDX_A647D432E7D23F1A (person_type_id), INDEX IDX_A647D432E946114A (province_id), INDEX IDX_A647D43273F74AD4 (territory_id), INDEX IDX_A647D432131A4F72 (commune_id), INDEX IDX_A647D432DF1E57AB (quartier_id), INDEX IDX_A647D4328BAC62AF (city_id), INDEX IDX_A647D4325E0D5582 (village_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE complaint (id VARCHAR(16) NOT NULL, complaint_type_id VARCHAR(16) NOT NULL, current_workflow_step_id VARCHAR(16) DEFAULT NULL, incident_cause_id VARCHAR(16) NOT NULL, road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, internal_resolution_decision_id VARCHAR(16) DEFAULT NULL, complainant_decision_id VARCHAR(16) DEFAULT NULL, satisfaction_follow_up_result_id VARCHAR(16) DEFAULT NULL, escalation_level_id VARCHAR(16) DEFAULT NULL, complainant_id VARCHAR(16) NOT NULL, current_workflow_action_id VARCHAR(16) NOT NULL, assigned_to_id VARCHAR(16) DEFAULT NULL, current_assignee_id VARCHAR(16) DEFAULT NULL, declaration_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', description LONGTEXT DEFAULT NULL, location_detail LONGTEXT NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, receivability_decision_justification LONGTEXT DEFAULT NULL, merits_analysis LONGTEXT DEFAULT NULL, resolution_proposal LONGTEXT DEFAULT NULL, internal_decision_comments LONGTEXT DEFAULT NULL, execution_actions_description LONGTEXT DEFAULT NULL, person_in_charge_of_execution VARCHAR(255) DEFAULT NULL, satisfaction_follow_up_comments LONGTEXT DEFAULT NULL, escalation_comments LONGTEXT DEFAULT NULL, closure_reason LONGTEXT DEFAULT NULL, closure_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_5F2732B5B0046CC2 (complaint_type_id), INDEX IDX_5F2732B59FB715DA (current_workflow_step_id), INDEX IDX_5F2732B59E27466D (incident_cause_id), INDEX IDX_5F2732B57D3C2BE7 (road_axis_id), INDEX IDX_5F2732B564D218E (location_id), INDEX IDX_5F2732B5FF3F2916 (internal_resolution_decision_id), INDEX IDX_5F2732B54F1F13DA (complainant_decision_id), INDEX IDX_5F2732B5F5DACC29 (satisfaction_follow_up_result_id), INDEX IDX_5F2732B5596309D8 (escalation_level_id), INDEX IDX_5F2732B54C422040 (complainant_id), INDEX IDX_5F2732B5ACC32D3B (current_workflow_action_id), INDEX IDX_5F2732B5F4BD7827 (assigned_to_id), INDEX IDX_5F2732B5E738D6B9 (current_assignee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE complaint_consequence (id VARCHAR(16) NOT NULL, complaint_id VARCHAR(16) NOT NULL, consequence_type_id VARCHAR(16) NOT NULL, severity_id VARCHAR(16) NOT NULL, affected_unit_id VARCHAR(16) DEFAULT NULL, affected_asset_type_id VARCHAR(16) DEFAULT NULL, estimated_cost DOUBLE PRECISION DEFAULT NULL, impact_description LONGTEXT NOT NULL, affected_quantity DOUBLE PRECISION DEFAULT NULL, INDEX IDX_E3C4B485EDAE188E (complaint_id), INDEX IDX_E3C4B4852C5A1844 (consequence_type_id), INDEX IDX_E3C4B485F7527401 (severity_id), INDEX IDX_E3C4B485B234A58C (affected_unit_id), INDEX IDX_E3C4B485DF070B36 (affected_asset_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE complaint_history (id VARCHAR(16) NOT NULL, complaint_id VARCHAR(16) NOT NULL, old_workflow_step_id VARCHAR(16) DEFAULT NULL, new_workflow_step_id VARCHAR(16) NOT NULL, action_id VARCHAR(16) NOT NULL, actor_id VARCHAR(16) NOT NULL, comments LONGTEXT DEFAULT NULL, action_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_52F2901AEDAE188E (complaint_id), INDEX IDX_52F2901A2AB88299 (old_workflow_step_id), INDEX IDX_52F2901A55B9A578 (new_workflow_step_id), INDEX IDX_52F2901A9D32F035 (action_id), INDEX IDX_52F2901A10DAF24A (actor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE general_parameter (id VARCHAR(16) NOT NULL, category VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, display_order INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE location (id VARCHAR(16) NOT NULL, level_id VARCHAR(16) NOT NULL, parent_id VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_5E9E89CB5FB14BA7 (level_id), INDEX IDX_5E9E89CB727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE profile (id VARCHAR(16) NOT NULL, label VARCHAR(120) NOT NULL, permissions LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)', person_type VARCHAR(3) NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis (id VARCHAR(16) NOT NULL, start_location_id VARCHAR(16) DEFAULT NULL, end_location_id VARCHAR(16) DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_913B460B5C3A313A (start_location_id), INDEX IDX_913B460BC43C7F1 (end_location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE road_axis_location (road_axis_id VARCHAR(16) NOT NULL, location_id VARCHAR(16) NOT NULL, INDEX IDX_61A7AD527D3C2BE7 (road_axis_id), INDEX IDX_61A7AD5264D218E (location_id), PRIMARY KEY(road_axis_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE species_price (id VARCHAR(16) NOT NULL, species_type_id VARCHAR(16) NOT NULL, road_axis_id VARCHAR(16) NOT NULL, unit_id VARCHAR(16) NOT NULL, currency_id VARCHAR(16) DEFAULT NULL, price_per_unit DOUBLE PRECISION DEFAULT NULL, effective_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', expiration_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_732A6E4567D2FDDC (species_type_id), INDEX IDX_732A6E457D3C2BE7 (road_axis_id), INDEX IDX_732A6E45F8BD700D (unit_id), INDEX IDX_732A6E4538248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id VARCHAR(16) NOT NULL, profile_id VARCHAR(16) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(15) DEFAULT NULL, display_name VARCHAR(120) DEFAULT NULL, deleted TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', locked TINYINT(1) NOT NULL, person_type VARCHAR(8) DEFAULT NULL, INDEX IDX_8D93D649CCFA12B8 (profile_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_PHONE (phone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE victim (id VARCHAR(16) NOT NULL, complaint_id VARCHAR(16) NOT NULL, gender_id VARCHAR(16) DEFAULT NULL, vulnerability_degree_id VARCHAR(16) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(120) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, full_name VARCHAR(255) DEFAULT NULL, age INT DEFAULT NULL, victim_description LONGTEXT NOT NULL, INDEX IDX_5CC0F29EDAE188E (complaint_id), INDEX IDX_5CC0F29708A0E0 (gender_id), INDEX IDX_5CC0F292C351FDB (vulnerability_degree_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE workflow_action (id VARCHAR(16) NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, requires_comment TINYINT(1) NOT NULL, requires_file TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE workflow_step (id VARCHAR(16) NOT NULL, duration_unit_id VARCHAR(16) DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, position INT NOT NULL, is_initial TINYINT(1) NOT NULL, is_final TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, expected_duration INT DEFAULT NULL, INDEX IDX_626EE07BD3BC3A5 (duration_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE workflow_transition (id VARCHAR(16) NOT NULL, from_step_id VARCHAR(16) NOT NULL, to_step_id VARCHAR(16) NOT NULL, action_id VARCHAR(16) NOT NULL, role_required_id VARCHAR(16) DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_6A3A796FF5ECFD33 (from_step_id), INDEX IDX_6A3A796FFD2A2369 (to_step_id), INDEX IDX_6A3A796F9D32F035 (action_id), INDEX IDX_6A3A796FEF6AA4B6 (role_required_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF862293EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF86229367D2FDDC FOREIGN KEY (species_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF862293B234A58C FOREIGN KEY (affected_unit_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species ADD CONSTRAINT FK_EF862293A6A2CDC5 FOREIGN KEY (asset_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file ADD CONSTRAINT FK_B010289AEDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file ADD CONSTRAINT FK_B010289A9E2A35A8 FOREIGN KEY (file_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file ADD CONSTRAINT FK_B010289A71FE882C FOREIGN KEY (workflow_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file ADD CONSTRAINT FK_B010289AA2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D432E7D23F1A FOREIGN KEY (person_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D432E946114A FOREIGN KEY (province_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D43273F74AD4 FOREIGN KEY (territory_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D432131A4F72 FOREIGN KEY (commune_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D432DF1E57AB FOREIGN KEY (quartier_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D4328BAC62AF FOREIGN KEY (city_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant ADD CONSTRAINT FK_A647D4325E0D5582 FOREIGN KEY (village_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5B0046CC2 FOREIGN KEY (complaint_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B59FB715DA FOREIGN KEY (current_workflow_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B59E27466D FOREIGN KEY (incident_cause_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B57D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B564D218E FOREIGN KEY (location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5FF3F2916 FOREIGN KEY (internal_resolution_decision_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B54F1F13DA FOREIGN KEY (complainant_decision_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5F5DACC29 FOREIGN KEY (satisfaction_follow_up_result_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5596309D8 FOREIGN KEY (escalation_level_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B54C422040 FOREIGN KEY (complainant_id) REFERENCES complainant (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5ACC32D3B FOREIGN KEY (current_workflow_action_id) REFERENCES workflow_action (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5E738D6B9 FOREIGN KEY (current_assignee_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence ADD CONSTRAINT FK_E3C4B485EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence ADD CONSTRAINT FK_E3C4B4852C5A1844 FOREIGN KEY (consequence_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence ADD CONSTRAINT FK_E3C4B485F7527401 FOREIGN KEY (severity_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence ADD CONSTRAINT FK_E3C4B485B234A58C FOREIGN KEY (affected_unit_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence ADD CONSTRAINT FK_E3C4B485DF070B36 FOREIGN KEY (affected_asset_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history ADD CONSTRAINT FK_52F2901AEDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history ADD CONSTRAINT FK_52F2901A2AB88299 FOREIGN KEY (old_workflow_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history ADD CONSTRAINT FK_52F2901A55B9A578 FOREIGN KEY (new_workflow_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history ADD CONSTRAINT FK_52F2901A9D32F035 FOREIGN KEY (action_id) REFERENCES workflow_action (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history ADD CONSTRAINT FK_52F2901A10DAF24A FOREIGN KEY (actor_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB5FB14BA7 FOREIGN KEY (level_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB727ACA70 FOREIGN KEY (parent_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis ADD CONSTRAINT FK_913B460B5C3A313A FOREIGN KEY (start_location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis ADD CONSTRAINT FK_913B460BC43C7F1 FOREIGN KEY (end_location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_location ADD CONSTRAINT FK_61A7AD527D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_location ADD CONSTRAINT FK_61A7AD5264D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E4567D2FDDC FOREIGN KEY (species_type_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E457D3C2BE7 FOREIGN KEY (road_axis_id) REFERENCES road_axis (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E45F8BD700D FOREIGN KEY (unit_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price ADD CONSTRAINT FK_732A6E4538248176 FOREIGN KEY (currency_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim ADD CONSTRAINT FK_5CC0F29EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim ADD CONSTRAINT FK_5CC0F29708A0E0 FOREIGN KEY (gender_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim ADD CONSTRAINT FK_5CC0F292C351FDB FOREIGN KEY (vulnerability_degree_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_step ADD CONSTRAINT FK_626EE07BD3BC3A5 FOREIGN KEY (duration_unit_id) REFERENCES general_parameter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition ADD CONSTRAINT FK_6A3A796FF5ECFD33 FOREIGN KEY (from_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition ADD CONSTRAINT FK_6A3A796FFD2A2369 FOREIGN KEY (to_step_id) REFERENCES workflow_step (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition ADD CONSTRAINT FK_6A3A796F9D32F035 FOREIGN KEY (action_id) REFERENCES workflow_action (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition ADD CONSTRAINT FK_6A3A796FEF6AA4B6 FOREIGN KEY (role_required_id) REFERENCES profile (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF862293EDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF86229367D2FDDC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF862293B234A58C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affected_species DROP FOREIGN KEY FK_EF862293A6A2CDC5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file DROP FOREIGN KEY FK_B010289AEDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file DROP FOREIGN KEY FK_B010289A9E2A35A8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file DROP FOREIGN KEY FK_B010289A71FE882C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE attached_file DROP FOREIGN KEY FK_B010289AA2B28FE8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D432E7D23F1A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D432E946114A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D43273F74AD4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D432131A4F72
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D432DF1E57AB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D4328BAC62AF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complainant DROP FOREIGN KEY FK_A647D4325E0D5582
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5B0046CC2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B59FB715DA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B59E27466D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B57D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B564D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5FF3F2916
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B54F1F13DA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5F5DACC29
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5596309D8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B54C422040
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5ACC32D3B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5F4BD7827
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5E738D6B9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence DROP FOREIGN KEY FK_E3C4B485EDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence DROP FOREIGN KEY FK_E3C4B4852C5A1844
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence DROP FOREIGN KEY FK_E3C4B485F7527401
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence DROP FOREIGN KEY FK_E3C4B485B234A58C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_consequence DROP FOREIGN KEY FK_E3C4B485DF070B36
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history DROP FOREIGN KEY FK_52F2901AEDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history DROP FOREIGN KEY FK_52F2901A2AB88299
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history DROP FOREIGN KEY FK_52F2901A55B9A578
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history DROP FOREIGN KEY FK_52F2901A9D32F035
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE complaint_history DROP FOREIGN KEY FK_52F2901A10DAF24A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB5FB14BA7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis DROP FOREIGN KEY FK_913B460B5C3A313A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis DROP FOREIGN KEY FK_913B460BC43C7F1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_location DROP FOREIGN KEY FK_61A7AD527D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE road_axis_location DROP FOREIGN KEY FK_61A7AD5264D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E4567D2FDDC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E457D3C2BE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E45F8BD700D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE species_price DROP FOREIGN KEY FK_732A6E4538248176
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649CCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim DROP FOREIGN KEY FK_5CC0F29EDAE188E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim DROP FOREIGN KEY FK_5CC0F29708A0E0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE victim DROP FOREIGN KEY FK_5CC0F292C351FDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_step DROP FOREIGN KEY FK_626EE07BD3BC3A5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition DROP FOREIGN KEY FK_6A3A796FF5ECFD33
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition DROP FOREIGN KEY FK_6A3A796FFD2A2369
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition DROP FOREIGN KEY FK_6A3A796F9D32F035
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workflow_transition DROP FOREIGN KEY FK_6A3A796FEF6AA4B6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE affected_species
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE attached_file
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complainant
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complaint
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complaint_consequence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE complaint_history
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE general_parameter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE location
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE road_axis_location
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE species_price
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE victim
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE workflow_action
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE workflow_step
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE workflow_transition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
