<?php

namespace App\Model;

final class NewParV2Model
{
    public function __construct(
        public readonly string $koboId,
        public readonly array $rawPayload,
        public readonly ?string $formhubUuid = null,
        public readonly ?\DateTimeImmutable $startAt = null,
        public readonly ?\DateTimeImmutable $endAt = null,
        public readonly ?string $province = null,
        public readonly ?string $axeRoutier = null,
        public readonly ?string $lieuActifAffecte = null,
        public readonly ?string $nomPersonneInterview = null,
        public readonly ?string $qualitePersInterviewe = null,
        public readonly ?\DateTimeImmutable $dateInventaire = null,
        public readonly ?string $gpsActif = null,
        public readonly ?string $nomChefMenageCm = null,
        public readonly ?string $sexeChefMenage = null,
        public readonly ?int $ageChefMenage = null,
        public readonly ?string $degreVulnerabiliteChefMenage = null,
        public readonly ?string $etatCivilChefMenage = null,
        public readonly ?int $nombreCompositionMenage = null,
        public readonly ?array $groupFx3uw05 = null,
        public readonly ?array $groupCd71m48 = null,
        public readonly ?string $sourceRevenue = null,
        public readonly ?string $sourceEnergieLumiere = null,
        public readonly ?string $sourceEnergieCuisine = null,
        public readonly ?string $sourceEauPotable = null,
        public readonly ?string $typeActifAffect = null,
        public readonly ?array $groupXh1rg07 = null,
        public readonly ?string $etesVousInformeRelocalis = null,
        public readonly ?string $acceptezRelocalis = null,
        public readonly ?string $conditionRelocalis = null,
        public readonly ?string $voulezVousLaisserQuelqu = null,
        public readonly ?string $bonneChoixRelocaliser = null,
        public readonly ?string $enCasProbleme = null,
        public readonly ?string $photo1 = null,
        public readonly ?string $photo2 = null,
        public readonly ?string $nomEnqueteur = null,
        public readonly ?float $dureeInterviewMinutes = null,
        public readonly ?string $koboVersion = null,
        public readonly ?string $metaInstanceId = null,
        public readonly ?string $metaDeprecatedId = null,
        public readonly ?string $xformIdString = null,
        public readonly ?string $koboUuid = null,
        public readonly ?array $attachments = null,
        public readonly ?string $koboStatus = null,
        public readonly ?array $geolocation = null,
        public readonly ?\DateTimeImmutable $submissionTime = null,
        public readonly ?array $tags = null,
        public readonly ?array $notes = null,
        public readonly ?array $validationStatus = null,
        public readonly ?string $submittedBy = null,
        public readonly ?string $metaRootUuid = null,
    ) {
    }

    public static function fromKoboPayload(array $payload, ?string $koboId = null): self
    {
        $resolvedKoboId = $koboId ?? self::getScalarString($payload, '_id');
        if ($resolvedKoboId === null || $resolvedKoboId === '') {
            throw new \InvalidArgumentException('koboId is required');
        }

        return new self(
            koboId: $resolvedKoboId,
            rawPayload: $payload,
            formhubUuid: self::getScalarString($payload, 'formhub/uuid'),
            startAt: self::parseDateTimeNullable(self::getScalarString($payload, 'start')),
            endAt: self::parseDateTimeNullable(self::getScalarString($payload, 'end')),
            province: self::getScalarString($payload, 'province'),
            axeRoutier: self::getScalarString($payload, 'Axe_routier'),
            lieuActifAffecte: self::getScalarString($payload, 'Lieu_actif_affecte'),
            nomPersonneInterview: self::getScalarString($payload, 'Nom_de_la_personne_interview'),
            qualitePersInterviewe: self::getScalarString($payload, 'Qualite_Pers_Interviewe'),
            dateInventaire: self::parseDateNullable(self::getScalarString($payload, 'Date_de_l_inventaire')),
            gpsActif: self::getScalarString($payload, 'GPS_actif'),
            nomChefMenageCm: self::getScalarString($payload, 'Nom_du_chef_du_m_nage_CM'),
            sexeChefMenage: self::getScalarString($payload, 'Sexe_du_chef_du_m_nage'),
            ageChefMenage: self::parseIntNullable(self::getScalarString($payload, 'Age_du_Chef_de_m_nage')),
            degreVulnerabiliteChefMenage: self::getScalarString($payload, 'Degr_de_vuln_rabilit_du_chef_de_m_nage'),
            etatCivilChefMenage: self::getScalarString($payload, 'Etat_civil_du_Chef_de_m_nage'),
            nombreCompositionMenage: self::parseIntNullable(self::getScalarString($payload, 'Quel_est_le_nombre_de_la_compo')),
            groupFx3uw05: self::getArrayNullable($payload, 'group_fx3uw05'),
            groupCd71m48: self::getArrayNullable($payload, 'group_cd71m48'),
            sourceRevenue: self::getScalarString($payload, 'group_nr46g97/Source_de_revenue'),
            sourceEnergieLumiere: self::getScalarString($payload, 'group_xi6vv61/Source_d_nergie_pour_la_lumi_re'),
            sourceEnergieCuisine: self::getScalarString($payload, 'group_xi6vv61/Source_d_nergie_pour_la_cuisine'),
            sourceEauPotable: self::getScalarString($payload, 'group_xi6vv61/Source_d_eau_potable'),
            typeActifAffect: self::getScalarString($payload, 'Type_actif_affect'),
            groupXh1rg07: self::getArrayNullable($payload, 'group_xh1rg07'),
            etesVousInformeRelocalis: self::getScalarString($payload, 'group_vj7oz54/Etes_vous_inform_qu_vous_serez_relocalis'),
            acceptezRelocalis: self::getScalarString($payload, 'group_vj7oz54/Acceptez_vous_d_tre_relocalis_'),
            conditionRelocalis: self::getScalarString($payload, 'group_vj7oz54/A_quelle_condition_a_vous_d_tre_relocalis'),
            voulezVousLaisserQuelqu: self::getScalarString($payload, 'group_vj7oz54/Voulez_vous_laisser_ici_quelqu'),
            bonneChoixRelocaliser: self::getScalarString($payload, 'group_vj7oz54/Est_ce_une_bonne_cho_de_vous_relocaliser'),
            enCasProbleme: self::getScalarString($payload, 'group_vj7oz54/En_cas_de_probl_me_pendant_le_'),
            photo1: self::getScalarString($payload, 'Photo1_de_l_actif_affect'),
            photo2: self::getScalarString($payload, 'Photo2_de_l_actif_affect'),
            nomEnqueteur: self::getScalarString($payload, 'Nom_enqueteur'),
            dureeInterviewMinutes: self::parseFloatNullable(self::getScalarString($payload, 'Dur_e_de_l_interview_en_minute')),
            koboVersion: self::getScalarString($payload, '__version__'),
            metaInstanceId: self::getScalarString($payload, 'meta/instanceID'),
            metaDeprecatedId: self::getScalarString($payload, 'meta/deprecatedID'),
            xformIdString: self::getScalarString($payload, '_xform_id_string'),
            koboUuid: self::getScalarString($payload, '_uuid'),
            attachments: self::getArrayNullable($payload, '_attachments'),
            koboStatus: self::getScalarString($payload, '_status'),
            geolocation: self::getArrayNullable($payload, '_geolocation'),
            submissionTime: self::parseDateTimeNullable(self::getScalarString($payload, '_submission_time')),
            tags: self::getArrayNullable($payload, '_tags'),
            notes: self::getArrayNullable($payload, '_notes'),
            validationStatus: self::getArrayNullable($payload, '_validation_status'),
            submittedBy: self::getScalarString($payload, '_submitted_by'),
            metaRootUuid: self::getScalarString($payload, 'meta/rootUuid'),
        );
    }

    private static function getScalarString(array $payload, string $key): ?string
    {
        if (!array_key_exists($key, $payload)) {
            return null;
        }

        $value = $payload[$key];
        if ($value === null) {
            return null;
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return null;
    }

    private static function getArrayNullable(array $payload, string $key): ?array
    {
        $value = $payload[$key] ?? null;
        if (!is_array($value)) {
            return null;
        }

        return $value;
    }

    private static function parseIntNullable(?string $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    private static function parseFloatNullable(?string $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private static function parseDateTimeNullable(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function parseDateNullable(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        if ($date === false) {
            return null;
        }

        return $date;
    }
}

