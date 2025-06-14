<?php

namespace App\Constant;

class GeneralParameterPersonType
{
    public const CATEGORY_PERSON_TYPE = 'PersonType';

    // Codes et Valeurs pour les types de personne
    public const INDIVIDUAL_CODE = 'INDIVIDUAL';
    public const INDIVIDUAL_VALUE = 'Personne Physique';

    public const LEGAL_ENTITY_CODE = 'LEGAL_ENTITY';
    public const LEGAL_ENTITY_VALUE = 'Personne Morale (Entreprise/Organisation)';

    public const ANONYMOUS_CODE = 'ANONYMOUS';
    public const ANONYMOUS_VALUE = 'Anonyme';

    public const GOVERNMENT_AGENT_CODE = 'GOV_AGENT';
    public const GOVERNMENT_AGENT_VALUE = 'Agent Gouvernemental'; // Si des agents peuvent aussi être des plaignants

    public const LOCAL_LEADER_CODE = 'LOCAL_LEADER';
    public const LOCAL_LEADER_VALUE = 'Leader Local';
}
