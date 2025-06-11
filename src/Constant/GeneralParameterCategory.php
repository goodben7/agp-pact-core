<?php

namespace App\Constant;

class GeneralParameterCategory
{
    public const COMPLAINT_TYPE = 'ComplaintType';
    public const INCIDENT_CAUSE = 'IncidentCause';
    public const INTERNAL_DECISION = 'InternalDecision'; // Décision interne sur la résolution
    public const COMPLAINANT_DECISION = 'ComplainantDecision'; // Décision du plaignant sur la proposition
    public const SATISFACTION_RESULT = 'SatisfactionResult'; // Résultat du suivi de satisfaction
    public const ESCALATION_LEVEL = 'EscalationLevel'; // Niveau d'escalade (CI, Justice)

    public const PERSON_TYPE = 'PersonType'; // Type de personne (Physique, Morale, Anonyme)
    public const GENDER = 'Gender'; // Genre (Homme, Femme, Autre)
    public const VULNERABILITY_DEGREE = 'VulnerabilityDegree'; // Degré de vulnérabilité

    public const FILE_TYPE = 'FileType'; // Type de fichier (Image, Document, Audio, Vidéo)

    public const LOCATION_LEVEL = 'LocationLevel'; // Niveau de localisation (Province, Territoire, Commune, Quartier, Ville, Village)

    public const COMPANY_TYPE = 'CompanyType'; // Type d'entreprise (Entreprise, ONG Spécialisée, CLRGP, CI, MdC, Autre)

    public const CONSEQUENCE_TYPE = 'ConsequenceType'; // Type de conséquence (Dommage Matériel, Perte de Livelihood, Impact Environnemental, etc.)
    public const CONSEQUENCE_SEVERITY = 'ConsequenceSeverity'; // Gravité de la conséquence (Mineure, Modérée, Majeure, Critique)
    public const ASSET_TYPE = 'AssetType'; // Type d'actif affecté (Bétail, Culture, Bâtiment, Personne, etc.)
    public const SPECIES_TYPE = 'SpeciesType'; // Type d'espèce (Vache, Chèvre, Maïs, Manguier, etc.)
    public const AFFECTED_UNIT = 'AffectedUnit'; // Unité de mesure des quantités affectées (Ha, Personnes, Animaux, Jours, Épisodes, Têtes, Arbres, Individus)

    public const DURATION_UNIT = 'DurationUnit'; // Unité de durée (Jours, Heures, Semaines, Mois)

    public const CURRENCY = 'Currency'; // Devise (USD, CDF, etc.)

    public const FORM_FIELD_TYPE = 'FormFieldType'; // Type de champ de formulaire (text, textarea, select, file_upload, gps_picker, etc.)
    public const WIDGET_TYPE = 'WidgetType'; // Type de widget UI personnalisé (MapPickerWidget, AffectedSpeciesEditor, etc.)
}
