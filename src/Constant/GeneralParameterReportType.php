<?php

namespace App\Constant;

class GeneralParameterReportType
{
    public const CATEGORY_REPORT_TYPE = 'ReportType';

    // Codes et Valeurs pour les types de rapport
    public const COMPLAINT_SUMMARY_CODE = 'COMPLAINT_SUMMARY';
    public const COMPLAINT_SUMMARY_VALUE = 'Rapport Récapitulatif des Plaintes';

    public const PAP_IMPACT_REPORT_CODE = 'PAP_IMPACT_REPORT';
    public const PAP_IMPACT_REPORT_VALUE = 'Rapport d\'Impact des PAP';

    public const WORKFLOW_PERFORMANCE_CODE = 'WORKFLOW_PERFORMANCE';
    public const WORKFLOW_PERFORMANCE_VALUE = 'Rapport de Performance du Workflow';

    public const AUDIT_TRAIL_CODE = 'AUDIT_TRAIL';
    public const AUDIT_TRAIL_VALUE = 'Rapport d\'Audit des Actions';
}
