<?php

namespace App\Constant;

class GeneralParameterAssetType
{
    public const CATEGORY_ASSET_TYPE = 'AssetType';

    // Codes and Values for asset types
    public const ASSET_CROP_CODE = 'CROP';
    public const ASSET_CROP_VALUE = 'Culture Agricole';

    public const ASSET_LIVESTOCK_CODE = 'LIVESTOCK';
    public const ASSET_LIVESTOCK_VALUE = 'Bétail';

    public const ASSET_BUILDING_CODE = 'BUILDING';
    public const ASSET_BUILDING_VALUE = 'Bâtiment/Structure';

    public const ASSET_VEHICLE_CODE = 'VEHICLE';
    public const ASSET_VEHICLE_VALUE = 'Véhicule';

    public const ASSET_NATURAL_RESOURCE_CODE = 'NATURAL_RESOURCE';
    public const ASSET_NATURAL_RESOURCE_VALUE = 'Ressource Naturelle (Eau, Sol, Forêt)';

    public const ASSET_INFRASTRUCTURE_CODE = 'INFRASTRUCTURE';
    public const ASSET_INFRASTRUCTURE_VALUE = 'Infrastructure (Route, Pont, Conduit)';

    public const ASSET_PERSONAL_PROPERTY_CODE = 'PERSONAL_PROPERTY';
    public const ASSET_PERSONAL_PROPERTY_VALUE = 'Bien Personnel (Hors Bâtiment/Véhicule)';

    public const ASSET_HUMAN_CAPITAL_CODE = 'HUMAN_CAPITAL';
    public const ASSET_HUMAN_CAPITAL_VALUE = 'Capital Humain (Personne)'; // Used in context of injury/fatality

    public const ASSET_OTHER_CODE = 'OTHER';
    public const ASSET_OTHER_VALUE = 'Autre Actif';
}
