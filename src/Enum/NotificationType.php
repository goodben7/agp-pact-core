<?php

namespace App\Enum;

class NotificationType
{
    // === PLAINTES ===
    public const COMPLAINT_REGISTERED = 'cmp_reg';
    public const COMPLAINT_UPDATED = 'cmp_upd';
    public const COMPLAINT_CLOSED = 'cmp_cls';
    public const COMPLAINT_REOPENED = 'cmp_rpo';

    // === TRANSITIONS & TRAITEMENT ===
    public const WORKFLOW_STEP_VALIDATED = 'wf_val';
    public const WORKFLOW_STEP_REJECTED = 'wf_rej';
    public const TRANSITION_EXECUTED = 'wf_trx';
    public const COMMENT_ADDED = 'cmt_add';
    public const FILE_ATTACHED = 'fil_add';

    // === VICTIMES ET CONSÉQUENCES ===
    public const VICTIM_ADDED = 'vic_add';
    public const VICTIM_UPDATED = 'vic_upd';
    public const CONSEQUENCE_DECLARED = 'con_add';
    public const AFFECTED_SPECIES_DECLARED = 'spe_add';

    // === LOCALISATION ===
    public const LOCATION_ASSIGNED = 'loc_add';
    public const LOCATION_UPDATED = 'loc_upd';

    // === NOTIFICATIONS UTILISATEUR ===
    public const NEW_ASSIGNMENT = 'usr_asg'; // Affecté à une plainte
    public const ACTION_REQUIRED = 'usr_req'; // Une action t'est demandée
    public const NOTIFICATION_SENT = 'ntf_snt'; // Confirmation qu'une notif est partie

    // === SYSTÈME / ADMIN ===
    public const USER_ACCOUNT_CREATED = 'usr_new';
    public const RESET_PASSWORD = 'res_pwd';
    public const ROLE_ASSIGNED = 'usr_rol';
    public const SYSTEM_UPDATE = 'sys_upd';

    public static function getAll(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }

    public static function getGrouped(): array
    {
        return [
            'complaint' => [
                self::COMPLAINT_REGISTERED,
                self::COMPLAINT_UPDATED,
                self::COMPLAINT_CLOSED,
                self::COMPLAINT_REOPENED,
            ],
            'workflow' => [
                self::WORKFLOW_STEP_VALIDATED,
                self::WORKFLOW_STEP_REJECTED,
                self::TRANSITION_EXECUTED,
                self::COMMENT_ADDED,
                self::FILE_ATTACHED,
            ],
            'impact' => [
                self::VICTIM_ADDED,
                self::VICTIM_UPDATED,
                self::CONSEQUENCE_DECLARED,
                self::AFFECTED_SPECIES_DECLARED,
            ],
            'location' => [
                self::LOCATION_ASSIGNED,
                self::LOCATION_UPDATED,
            ],
            'user_notifications' => [
                self::NEW_ASSIGNMENT,
                self::ACTION_REQUIRED,
                self::NOTIFICATION_SENT,
            ],
            'system' => [
                self::USER_ACCOUNT_CREATED,
                self::ROLE_ASSIGNED,
                self::SYSTEM_UPDATE,
            ],
        ];
    }
}
