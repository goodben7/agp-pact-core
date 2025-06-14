<?php

namespace App\Constant;

class GeneralParameterAffectedUnit
{
    public const CATEGORY_AFFECTED_UNIT = 'AffectedUnit';

    // Codes and Values for affected units
    public const UNIT_HEADS_CODE = 'HEADS';         // For livestock (têtes d'animaux)
    public const UNIT_HEADS_VALUE = 'Têtes';

    public const UNIT_HECTARES_CODE = 'HECTARES';     // For land/crops
    public const UNIT_HECTARES_VALUE = 'Hectares';

    public const UNIT_SQUARE_METERS_CODE = 'SQ_METERS'; // For buildings/land area
    public const UNIT_SQUARE_METERS_VALUE = 'Mètres Carrés';

    public const UNIT_INDIVIDUALS_CODE = 'INDIVIDUALS'; // For people or single items
    public const UNIT_INDIVIDUALS_VALUE = 'Individus';

    public const UNIT_TREES_CODE = 'TREES';           // For trees
    public const UNIT_TREES_VALUE = 'Arbres';

    public const UNIT_KILOGRAMS_CODE = 'KILOGRAMS';     // For weight (e.g., harvested crops)
    public const UNIT_KILOGRAMS_VALUE = 'Kilogrammes';

    public const UNIT_LITERS_CODE = 'LITERS';         // For liquids
    public const UNIT_LITERS_VALUE = 'Litres';

    public const UNIT_CASES_CODE = 'CASES';           // For incidents or episodes
    public const UNIT_CASES_VALUE = 'Cas';

    public const UNIT_HOURS_CODE = 'HOURS';           // For time affected
    public const UNIT_HOURS_VALUE = 'Heures';

    public const UNIT_DAYS_CODE = 'DAYS';             // For time affected
    public const UNIT_DAYS_VALUE = 'Jours';

    public const UNIT_OTHER_CODE = 'OTHER';
    public const UNIT_OTHER_VALUE = 'Autre Unité';
}
