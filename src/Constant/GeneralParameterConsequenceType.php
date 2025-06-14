<?php

namespace App\Constant;

class GeneralParameterConsequenceType
{
    public const CATEGORY_CONSEQUENCE_TYPE = 'ConsequenceType';

    // Codes and Values for consequence types
    public const MATERIAL_DAMAGE_CODE = 'MATERIAL_DAMAGE';
    public const MATERIAL_DAMAGE_VALUE = 'Dommage Matériel';

    public const LIVELIHOOD_LOSS_CODE = 'LIVELIHOOD_LOSS';
    public const LIVELIHOOD_LOSS_VALUE = 'Perte de Moyens de Subsistance';

    public const ENVIRONMENTAL_IMPACT_CODE = 'ENV_IMPACT';
    public const ENVIRONMENTAL_IMPACT_VALUE = 'Impact Environnemental';

    public const PERSONAL_INJURY_CODE = 'PERSONAL_INJURY';
    public const PERSONAL_INJURY_VALUE = 'Blessure Corporelle';

    public const PSYCHOLOGICAL_HARM_CODE = 'PSYCHOLOGICAL_HARM';
    public const PSYCHOLOGICAL_HARM_VALUE = 'Préjudice Psychologique';

    public const FATALITY_CODE = 'FATALITY';
    public const FATALITY_VALUE = 'Décès';

    public const DISPLACEMENT_CODE = 'DISPLACEMENT';
    public const DISPLACEMENT_VALUE = 'Déplacement de Personnes';

    public const ACCESS_LOSS_CODE = 'ACCESS_LOSS';
    public const ACCESS_LOSS_VALUE = 'Perte d\'Accès (à la terre, ressources)';

    public const OTHER_CODE = 'OTHER';
    public const OTHER_VALUE = 'Autre Conséquence';
}
