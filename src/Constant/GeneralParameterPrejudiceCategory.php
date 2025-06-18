<?php

namespace App\Constant;

class GeneralParameterPrejudiceCategory
{
    // Catégorie : Mauvaise gestion du projet
    public const MISMANAGEMENT = 'MIS'; // Mauvaise gestion du projet
    public const FAVORITISM_CORRUPTION = 'FAV'; // Cas de favoritisme ou de corruption
    public const RESOURCE_MISUSE = 'RMU'; // Mauvaise utilisation des ressources
    public const HARMFUL_DECISION = 'HDC'; // Décision ou activité préjudiciable

    // Catégorie : Impacts sociaux
    public const COMMUNITY_RELATIONS = 'COM'; // Mauvaises relations communautaires
    public const SOCIAL_DISCRIMINATION = 'SDI'; // Discrimination sociale ou de genre

    // Catégorie : Conditions de travail
    public const WORK_CONDITIONS = 'WCO'; // Conditions de travail
    public const TRAFFIC_ACCIDENT = 'TRA'; // Accident de circulation
    public const WORKPLACE_ACCIDENT = 'WAC'; // Accident sur les lieux de travail
    public const DANGEROUS_WORK = 'DAN'; // Travail dangereux
    public const HEALTH_SAFETY = 'HSE'; // Risque pour la santé/sécurité
    public const OCCUPATIONAL_DISEASE = 'DIS'; // Maladie professionnelle
    public const WORKERS_RIGHTS = 'WRI'; // Non-respect des droits
    public const FORCED_LABOUR = 'FOR'; // Travail forcé
    public const CHILD_LABOUR = 'CHL'; // Travail d’enfants

    // Catégorie : Impacts environnementaux
    public const WASTE_DISCHARGE = 'WST'; // Déversement déchets
    public const ENVIRONMENT_DESTRUCTION = 'ENV'; // Destruction arbres/terres
    public const PGES_NON_COMPLIANCE = 'PGS'; // Non-respect PGES
    public const NUISANCE = 'NUI'; // Nuisances (bruit, poussière...)

    // Catégorie : Réinstallation
    public const RESETTLEMENT_DISSATISFACTION = 'RED'; // Insatisfaction processus
    public const LOSS_AGRICULTURAL_ASSET = 'AGR'; // Perte d’actif agricole
    public const LOSS_BUILT_ASSET = 'BLT'; // Perte d’actif bâti
    public const LOSS_LAND_ASSET = 'LND'; // Perte d’actif foncier
    public const LOSS_INCOME = 'INC'; // Perte de revenus
    public const ACCESS_RESTRICTION = 'RES'; // Restriction d’accès

    // Catégorie : Ressources naturelles & biodiversité
    public const BIODIVERSITY_DESTRUCTION = 'BIO'; // Destruction biodiversité
    public const NATURAL_RESOURCE_LOSS = 'NAT'; // Perte ressources naturelles
    public const NATURAL_RESOURCE_MISMANAGEMENT = 'NRM'; // Mauvaise gestion ressource
    public const BIODIVERSITY_IMPACT = 'BIP'; // Impact biodiversité

    // Catégorie : Patrimoine culturel
    public const ARCHAEOLOGICAL_SITE_DAMAGE = 'ARC'; // Dégradation archéologique
    public const SACRED_SITE_DAMAGE = 'SAC'; // Dégradation lieu sacré
    public const GRAVE_DAMAGE = 'GRV'; // Dommages aux sépultures
    public const AESTHETIC_DAMAGE = 'EST'; // Dégradation esthétique

    // Catégorie : Plaintes sensibles (VBG/EAHS)
    public const GBV_PHYSICAL = 'VPH'; // Violence physique
    public const GBV_VERBAL = 'VVB'; // Violence verbale (discours de haine)
    public const GBV_PSYCHOLOGICAL = 'VPS'; // Violence psychologique
    public const GBV_SEXUAL = 'VSX'; // Violence sexuelle
    public const GBV_HARASSMENT = 'VHR'; // Harcèlement sexuel
    public const GBV_SOCIOECONOMIC = 'VSE'; // Violence socioéconomique

    /**
     * Retourne toutes les catégories sous forme de tableau associatif
     */
    public static function getAll(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }
}
