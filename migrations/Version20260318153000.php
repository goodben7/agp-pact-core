<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260318153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create par_v2 table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE par_v2 (
                id VARCHAR(16) NOT NULL,
                kobo_id BIGINT NOT NULL,
                formhub_uuid VARCHAR(64) DEFAULT NULL,
                start_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                end_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                province VARCHAR(255) DEFAULT NULL,
                axe_routier VARCHAR(255) DEFAULT NULL,
                lieu_actif_affecte VARCHAR(255) DEFAULT NULL,
                nom_personne_interview VARCHAR(255) DEFAULT NULL,
                qualite_pers_interviewe VARCHAR(255) DEFAULT NULL,
                date_inventaire DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
                gps_actif VARCHAR(255) DEFAULT NULL,
                nom_chef_menage_cm VARCHAR(255) DEFAULT NULL,
                sexe_chef_menage VARCHAR(8) DEFAULT NULL,
                age_chef_menage INT DEFAULT NULL,
                degre_vulnerabilite_chef_menage VARCHAR(255) DEFAULT NULL,
                etat_civil_chef_menage VARCHAR(255) DEFAULT NULL,
                nombre_composition_menage INT DEFAULT NULL,
                group_fx3uw05 JSON DEFAULT NULL,
                group_cd71m48 JSON DEFAULT NULL,
                source_revenue VARCHAR(255) DEFAULT NULL,
                source_energie_lumiere VARCHAR(255) DEFAULT NULL,
                source_energie_cuisine VARCHAR(255) DEFAULT NULL,
                source_eau_potable VARCHAR(255) DEFAULT NULL,
                type_actif_affect VARCHAR(255) DEFAULT NULL,
                group_xh1rg07 JSON DEFAULT NULL,
                etes_vous_informe_relocalis VARCHAR(32) DEFAULT NULL,
                acceptez_relocalis VARCHAR(32) DEFAULT NULL,
                condition_relocalis VARCHAR(255) DEFAULT NULL,
                voulez_vous_laisser_quelqu VARCHAR(32) DEFAULT NULL,
                bonne_choix_relocaliser VARCHAR(255) DEFAULT NULL,
                en_cas_probleme VARCHAR(255) DEFAULT NULL,
                photo1 VARCHAR(255) DEFAULT NULL,
                photo2 VARCHAR(255) DEFAULT NULL,
                nom_enqueteur VARCHAR(255) DEFAULT NULL,
                duree_interview_minutes DOUBLE PRECISION DEFAULT NULL,
                kobo_version VARCHAR(64) DEFAULT NULL,
                meta_instance_id VARCHAR(100) DEFAULT NULL,
                meta_deprecated_id VARCHAR(100) DEFAULT NULL,
                xform_id_string VARCHAR(100) DEFAULT NULL,
                kobo_uuid VARCHAR(100) DEFAULT NULL,
                attachments JSON DEFAULT NULL,
                kobo_status VARCHAR(50) DEFAULT NULL,
                geolocation JSON DEFAULT NULL,
                submission_time DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                tags JSON DEFAULT NULL,
                notes JSON DEFAULT NULL,
                validation_status JSON DEFAULT NULL,
                submitted_by VARCHAR(120) DEFAULT NULL,
                meta_root_uuid VARCHAR(100) DEFAULT NULL,
                raw_payload JSON NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE INDEX UNIQ_PAR_V2_KOBO_ID (kobo_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP TABLE par_v2
        SQL);
    }
}

