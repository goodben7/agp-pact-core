<?php

namespace App\Constant;

class GeneralParameterCompanyType
{
    public const CATEGORY_COMPANY_TYPE = 'CompanyType';

    // Codes et Valeurs pour les types d'entreprise
    public const PRIVATE_COMPANY_CODE = 'PRIVATE_COMPANY';
    public const PRIVATE_COMPANY_VALUE = 'Entreprise Privée';

    public const NGO_CODE = 'NGO';
    public const NGO_VALUE = 'Organisation Non Gouvernementale (ONG)';

    public const GOVERNMENT_AGENCY_CODE = 'GOV_AGENCY';
    public const GOVERNMENT_AGENCY_VALUE = 'Agence Gouvernementale';

    public const LOCAL_COMMUNITY_LEADER_CODE = 'LOCAL_COMMUNITY_LEADER';
    public const LOCAL_COMMUNITY_LEADER_VALUE = 'Leader Communautaire Local'; // Peut être une "entité" si c'est un groupe

    public const CLRGP_CODE = 'CLRGP'; // Cellule de Lutte contre le Racket et les Incivilités Routières et Portuaires
    public const CLRGP_VALUE = 'CLRGP (Cellule de Lutte contre le Racket et les Incivilités)';

    public const LOCAL_COMMITTEE_CODE = 'LOCAL_COMMITTEE';
    public const LOCAL_COMMITTEE_VALUE = 'Comité Locaux';

    public const INTERNAL_COMMITTEE_CODE = 'INTERNAL_COMMITTEE'; // Comité Interne de l'organisation
    public const INTERNAL_COMMITTEE_VALUE = 'Comité Interne';

    public const OTHER_CODE = 'OTHER';
    public const OTHER_VALUE = 'Autre';
}
