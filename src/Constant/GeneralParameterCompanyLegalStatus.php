<?php

namespace App\Constant;

class GeneralParameterCompanyLegalStatus
{
    public const CATEGORY_COMPANY_LEGAL_STATUS = 'CompanyLegalStatus';

    // Codes et Valeurs pour les statuts légaux des entreprises/organisations
    public const REGISTERED_COMPANY_CODE = 'REGISTERED_COMPANY';
    public const REGISTERED_COMPANY_VALUE = 'Entreprise Enregistrée'; // SARL, SA, EURL, etc.

    public const NON_PROFIT_ORGANIZATION_CODE = 'NON_PROFIT_ORGANIZATION';
    public const NON_PROFIT_ORGANIZATION_VALUE = 'Organisation à But Non Lucratif (ONG/Asbl)';

    public const GOVERNMENT_ENTITY_CODE = 'GOVERNMENT_ENTITY';
    public const GOVERNMENT_ENTITY_VALUE = 'Entité Gouvernementale'; // Ministère, agence publique

    public const INFORMAL_ASSOCIATION_CODE = 'INFORMAL_ASSOCIATION';
    public const INFORMAL_ASSOCIATION_VALUE = 'Association Informelle/Non Enregistrée'; // Groupe communautaire

    public const SOLE_PROPRIETORSHIP_CODE = 'SOLE_PROPRIETORSHIP';
    public const SOLE_PROPRIETORSHIP_VALUE = 'Entreprise Individuelle'; // Pour les auto-entrepreneurs, etc.

    public const UNKNOWN_CODE = 'UNKNOWN';
    public const UNKNOWN_VALUE = 'Statut Inconnu';

}
