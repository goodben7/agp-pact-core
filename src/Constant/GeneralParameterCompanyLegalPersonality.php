<?php

namespace App\Constant;

class GeneralParameterCompanyLegalPersonality
{
    public const CATEGORY_COMPANY_LEGAL_PERSONALITY = 'CompanyLegalPersonality';

    // Codes et Valeurs pour les personnalités juridiques des entreprises/organisations
    public const LEGAL_PERSON_CODE = 'LEGAL_PERSON';
    public const LEGAL_PERSON_VALUE = 'Personne Morale'; // A une existence juridique distincte

    public const NATURAL_PERSON_CODE = 'NATURAL_PERSON';
    public const NATURAL_PERSON_VALUE = 'Personne Physique'; // Pour les entreprises individuelles, liées à une personne physique

    public const NO_LEGAL_PERSONALITY_CODE = 'NO_LEGAL_PERSONALITY';
    public const NO_LEGAL_PERSONALITY_VALUE = 'Sans Personnalité Juridique';

}
