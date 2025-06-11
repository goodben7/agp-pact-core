<?php

namespace App\Constant;

class WorkflowStepName
{
    public const DECLARED = 'Declared'; // 'Déclarée'
    public const CLASSIFIED_ASSIGNED = 'Classified/Assigned'; // 'Classifiée/Assignée'
    public const RECEIVABLE = 'Receivable'; // 'Recevable'
    public const NON_RECEIVABLE = 'Non-Receivable'; // 'Non-Recevable'
    public const MERITS_EXAMINED = 'Merits Examined'; // 'Fondement examiné'
    public const PROPOSAL_DRAFTED = 'Proposal Drafted'; // 'Proposition rédigée'
    public const PROPOSAL_APPROVED = 'Proposal Approved'; // 'Proposition Approuvée'
    public const INTERNAL_PROPOSAL_REJECTED = 'Internal Proposal Rejected'; // 'Proposition Rejetée Interne'
    public const ACCEPTED_AWAITING_EXECUTION = 'Accepted, Awaiting Execution'; // 'Acceptée, En attente d\'exécution'
    public const REJECTED_BY_COMPLAINANT = 'Rejected by Complainant'; // 'Refusée par Plaignant'
    public const RESOLUTION_EXECUTED = 'Resolution Executed'; // 'Résolution Exécutée'
    public const FOLLOW_UP_COMPLETED = 'Follow-up Completed'; // 'Suivi Terminé'
    public const ESCALATED_CI = 'Escalated: CI'; // 'Escaladée: CI'
    public const ESCALATED_JUSTICE = 'Escalated: Justice'; // 'Escaladée: Justice'
    public const CLOSED = 'Closed'; // 'Clôturée'
}
