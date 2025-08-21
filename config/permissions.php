<?php

declare(strict_types=1);

use App\Model\Permission;

return static function (): iterable {

    yield Permission::new('ROLE_USER_CREATE', "Créér un utilisateur");
    yield Permission::new('ROLE_USER_LOCK', "Vérouiller/Déverrouiller un utilisateur");
    yield Permission::new('ROLE_USER_CHANGE_PWD', "Modifier mot de passe");
    yield Permission::new('ROLE_USER_DETAILS', "Consulter les détails d'un utilisateur");
    yield Permission::new('ROLE_USER_LIST', "Consulter la liste des utilisateurs");
    yield Permission::new('ROLE_USER_EDIT', "Editer les informations d'un utilisateur");
    yield Permission::new('ROLE_USER_DELETE', "Supprimer un utilisateur");
    yield Permission::new('ROLE_USER_SET_PROFILE', "Modifier le profil utilisateur");

    yield Permission::new('ROLE_PROFILE_CREATE', "Créer un profil utilisateur");
    yield Permission::new('ROLE_PROFILE_LIST', "Consulter la liste des profils utilisateur");
    yield Permission::new('ROLE_PROFILE_UPDATE', "Modifier un profil utilisateur");
    yield Permission::new('ROLE_PROFILE_DETAILS', "Consulter les détails d'un profil utilisateur");

    yield Permission::new('ROLE_NOTIFICATION_LIST', "Consulter la liste des notifications");
    yield Permission::new('ROLE_NOTIFICATION_DETAILS', "Consulter les détails d'une notification");
    yield Permission::new('ROLE_NOTIFICATION_CREATE', "Créer une notification");
    yield Permission::new('ROLE_NOTIFICATION_UPDATE', "Modifier une notification");


    yield Permission::new('ROLE_GENERAL_PARAMETER_LIST', "Consulter la liste des paramètres généraux");
    yield Permission::new('ROLE_GENERAL_PARAMETER_DETAILS', "Consulter un paramètre général");
    yield Permission::new('ROLE_GENERAL_PARAMETER_CREATE', "Créer un paramètre général");
    yield Permission::new('ROLE_GENERAL_PARAMETER_UPDATE', "Modifier un paramètre général");
    yield Permission::new('ROLE_GENERAL_PARAMETER_DELETE', "Supprimer un paramètre général");

    yield Permission::new('ROLE_COMPLAINT_LIST', "Consulter la liste des plaintes");
    yield Permission::new('ROLE_COMPLAINT_DETAILS', "Consulter les détails d'une plainte");
    yield Permission::new('ROLE_COMPLAINT_CREATE', "Créer une plainte");
    yield Permission::new('ROLE_COMPLAINT_UPDATE', "Modifier une plainte");
    yield Permission::new('ROLE_COMPLAINT_APPLY_ACTION', "Appliquer une action sur une plainte");

    yield Permission::new('ROLE_COMPLAINANT_LIST', 'Consulter la liste des plaignants');
    yield Permission::new('ROLE_COMPLAINANT_DETAILS', 'Consulter les détails d\'un plaignant');
    yield Permission::new('ROLE_COMPLAINANT_CREATE', 'Créer un plaignant');
    yield Permission::new('ROLE_COMPLAINANT_UPDATE', 'Modifier un plaignant');

    yield Permission::new('ROLE_COMPLAINT_TYPE_LIST', 'Consulter la liste des types de plainte');
    yield Permission::new('ROLE_COMPLAINT_TYPE_DETAILS', 'Consulter les détails d\'un type de plainte');
    yield Permission::new('ROLE_COMPLAINT_TYPE_CREATE', 'Créer un type de plainte');
    yield Permission::new('ROLE_COMPLAINT_TYPE_UPDATE', 'Modifier un type de plainte');

    yield Permission::new('ROLE_COMPLAINT_STATUS_LIST', 'Consulter la liste des statuts de plainte');
    yield Permission::new('ROLE_COMPLAINT_STATUS_DETAILS', 'Consulter les détails d\'un statut de plainte');
    yield Permission::new('ROLE_COMPLAINT_STATUS_CREATE', 'Créer un statut de plainte');
    yield Permission::new('ROLE_COMPLAINT_STATUS_UPDATE', 'Modifier un statut de plainte');

    yield Permission::new('ROLE_COMPLAINT_HISTORY_LIST', 'Consulter la liste des historiques de plainte');
    yield Permission::new('ROLE_COMPLAINT_HISTORY_DETAILS', 'Consulter les détails d\'un historique de plainte');
    yield Permission::new('ROLE_COMPLAINT_HISTORY_CREATE', 'Créer un historique de plainte');
    yield Permission::new('ROLE_COMPLAINT_HISTORY_UPDATE', 'Modifier un historique de plainte');

    yield Permission::new('ROLE_COMPLAINT_COMMENT_LIST', 'Consulter la liste des commentaires de plainte');
    yield Permission::new('ROLE_COMPLAINT_COMMENT_DETAILS', 'Consulter les détails d\'un commentaire de plainte');
    yield Permission::new('ROLE_COMPLAINT_COMMENT_CREATE', 'Créer un commentaire de plainte');
    yield Permission::new('ROLE_COMPLAINT_COMMENT_UPDATE', 'Modifier un commentaire de plainte');


    yield Permission::new('ROLE_AFFECTED_SPECIES_LIST', "Consulter la liste des espèces affectés");
    yield Permission::new('ROLE_AFFECTED_SPECIES_DETAILS', "Consulter les détails d'un espèce affecté");
    yield Permission::new('ROLE_AFFECTED_SPECIES_CREATE', "Créer un espèce affecté");
    yield Permission::new('ROLE_AFFECTED_SPECIES_UPDATE', "Modifier un espèce affecté");

    yield Permission::new('ROLE_ROAD_AXIS_LIST', 'Consulter la liste des axes routiers');
    yield Permission::new('ROLE_ROAD_AXIS_DETAILS', 'Consulter les détails d\'un axe routier');
    yield Permission::new('ROLE_ROAD_AXIS_CREATE', 'Créer un axe routier');
    yield Permission::new('ROLE_ROAD_AXIS_UPDATE', 'Modifier un axe routier');

    yield Permission::new('ROLE_LOCATION_LIST', 'Consulter la liste des localisations');
    yield Permission::new('ROLE_LOCATION_DETAILS', 'Consulter les détails d\'une localisation');
    yield Permission::new('ROLE_LOCATION_CREATE', 'Créer une localisation');
    yield Permission::new('ROLE_LOCATION_UPDATE', 'Modifier une localisation');
    yield Permission::new('ROLE_LOCATION_DELETE', 'Supprimer une localisation');

    yield Permission::new('ROLE_SPECIES_PRICE_LIST', 'Consulter la liste des prix des espèces');
    yield Permission::new('ROLE_SPECIES_PRICE_DETAILS', 'Consulter les détails d\'un prix d\'espèce');
    yield Permission::new('ROLE_SPECIES_PRICE_CREATE', 'Créer un prix d\'espèce');
    yield Permission::new('ROLE_SPECIES_PRICE_UPDATE', 'Modifier un prix d\'espèce');

    yield Permission::new('ROLE_VICTIM_LIST', 'Consulter la liste des victimes');
    yield Permission::new('ROLE_VICTIM_DETAILS', 'Consulter les détails d\'une victime');
    yield Permission::new('ROLE_VICTIM_CREATE', 'Créer une victime');
    yield Permission::new('ROLE_VICTIM_UPDATE', 'Modifier une victime');

    yield Permission::new('ROLE_WORKFLOW_STEP_LIST', 'Consulter la liste des étapes de traitement');
    yield Permission::new('ROLE_WORKFLOW_STEP_DETAILS', 'Consulter les détails d\'une étape de traitement');
    yield Permission::new('ROLE_WORKFLOW_STEP_CREATE', 'Créer une étape de traitement');
    yield Permission::new('ROLE_WORKFLOW_STEP_UPDATE', 'Modifier une étape de traitement');

    yield Permission::new('ROLE_WORKFLOW_ACTION_LIST', 'Consulter la liste des actions de traitement');
    yield Permission::new('ROLE_WORKFLOW_ACTION_DETAILS', 'Consulter les détails d\'une action de traitement');
    yield Permission::new('ROLE_WORKFLOW_ACTION_CREATE', 'Créer une action de traitement');
    yield Permission::new('ROLE_WORKFLOW_ACTION_UPDATE', 'Modifier une action de traitement');

    yield Permission::new('ROLE_WORKFLOW_TRANSITION_LIST', 'Consulter la liste des transitions de traitement');
    yield Permission::new('ROLE_WORKFLOW_TRANSITION_DETAILS', 'Consulter les détails d\'une transition de traitement');
    yield Permission::new('ROLE_WORKFLOW_TRANSITION_CREATE', 'Créer une transition de traitement');
    yield Permission::new('ROLE_WORKFLOW_TRANSITION_UPDATE', 'Modifier une transition de traitement');

    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_LIST', "Consulter la liste des modèles de notification");
    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_DETAILS', "Consulter les détails d'un modèle de notification");
    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_CREATE', "Créer un modèle de notification");
    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_UPDATE', "Modifier un modèle de notification");

    yield Permission::new('ROLE_CLASSIFY_ASSIGN_COMPLAINT', "Pour classer et assigner (action `classify_assign_action`)");

    yield Permission::new('ROLE_ACTIVITY_LOG_LIST', "Consulter la liste des activités");

    yield Permission::new('ROLE_COMPANY_CREATE', "Créer une entité");
    yield Permission::new('ROLE_COMPANY_LIST', "Consulter la liste des entités");
    yield Permission::new('ROLE_COMPANY_UPDATE', "Modifier une entité");
    yield Permission::new('ROLE_COMPANY_DETAILS', "Consulter les détails d'une entité");
    yield Permission::new('ROLE_COMPANY_DELETE', "Supprimer une entité");

    yield Permission::new('ROLE_COMPLAINT_CONSEQUENCE_LIST', 'Consulter la liste les conséquences des plaintes');
    yield Permission::new('ROLE_COMPLAINT_CONSEQUENCE_DETAILS', 'Voir les détails des conséquences des plaintes');
    yield Permission::new('ROLE_COMPLAINT_CONSEQUENCE_CREATE', 'Créer de nouvelles conséquences pour les plaintes');
    yield Permission::new('ROLE_COMPLAINT_CONSEQUENCE_UPDATE', 'Mettre à jour les conséquences des plaintes');

    yield Permission::new('ROLE_VICTIM_LIST', 'Consulter la liste des victimes');
    yield Permission::new('ROLE_VICTIM_DETAILS', 'Voir les détails des victimes');
    yield Permission::new('ROLE_VICTIM_CREATE', 'Créer de nouvelles victimes');
    yield Permission::new('ROLE_VICTIM_UPDATE', 'Modifier les victimes');

    yield Permission::new('ROLE_FAQ_LIST', 'Consulter la liste des FAQ');
    yield Permission::new('ROLE_FAQ_DETAILS', 'Voir les détails des FAQ');
    yield Permission::new('ROLE_FAQ_CREATE', 'Créer de nouvelles FAQ');
    yield Permission::new('ROLE_FAQ_UPDATE', 'Modifier les FAQ');

    yield Permission::new('ROLE_REPORT_TEMPLATE_LIST', 'Consulter la liste des modèles de rapports');
    yield Permission::new('ROLE_REPORT_TEMPLATE_DETAILS', 'Voir les détails des modèles de rapports');
    yield Permission::new('ROLE_REPORT_TEMPLATE_CREATE', 'Créer de nouvelles modèles de rapports');
    yield Permission::new('ROLE_REPORT_TEMPLATE_UPDATE', 'Modifier les modèles de rapports');

    yield Permission::new('ROLE_VIEW_GENERATED_REPORTS', 'Générer des modèles de rapports');

    yield Permission::new('ROLE_PAP_DETAILS', "Consulter les détails d'une Personne Affectée au Projet (PAP)");
    yield Permission::new('ROLE_PAP_LIST', "Consulter la liste des Personnes Affectées au Projet (PAP)");
    yield Permission::new('ROLE_PAP_CREATE', "Créer une Personne Affectée au Projet (PAP)");
    yield Permission::new('ROLE_PAP_UPDATE', "Modifier une Personne Affectée au Projet (PAP)");
    yield Permission::new('ROLE_PAP_DELETE', "Supprimer une Personne Affectée au Projet (PAP)");

    yield Permission::new('ROLE_PREJUDICE_DETAILS', "Consulter les détails d'un préjudice");
    yield Permission::new('ROLE_PREJUDICE_LIST', "Consulter la liste des préjudices");
    yield Permission::new('ROLE_PREJUDICE_CREATE', "Créer un préjudice");
    yield Permission::new('ROLE_PREJUDICE_UPDATE', "Modifier un préjudice");
    yield Permission::new('ROLE_PREJUDICE_DELETE', "Supprimer un préjudice");

    yield Permission::new('ROLE_MEMBER_DETAILS', "Consulter les détails d'un membre");
    yield Permission::new('ROLE_MEMBER_LIST', "Consulter la liste des membres");
    yield Permission::new('ROLE_MEMBER_CREATE', "Créer un membre");
    yield Permission::new('ROLE_MEMBER_UPDATE', "Modifier un membre");
    yield Permission::new('ROLE_USER_SET_ACCESS', "Modifier les accès d'un utilisateur");

    yield Permission::new('ROLE_SPECIES_LIST', "Consulter la liste des espèces");
    yield Permission::new('ROLE_SPECIES_DETAILS', "Consulter les détails d'une espèce");
    yield Permission::new('ROLE_SPECIES_CREATE', "Créer une espèce");
    yield Permission::new('ROLE_SPECIES_UPDATE', "Modifier une espèce");

    yield Permission::new('ROLE_CAUSE_LIST', "Consulter la liste des causes");
    yield Permission::new('ROLE_CAUSE_DETAILS', "Consulter les détails d'une cause");
    yield Permission::new('ROLE_CAUSE_CREATE', "Créer une cause");
    yield Permission::new('ROLE_CAUSE_UPDATE', "Modifier une cause");

    yield Permission::new('ROLE_OFFENDER_LIST', "Consulter la liste des contrevenants");
    yield Permission::new('ROLE_OFFENDER_DETAILS', "Consulter les détails d'un contrevenant");
    yield Permission::new('ROLE_OFFENDER_CREATE', "Créer un contrevenant");
    yield Permission::new('ROLE_OFFENDER_UPDATE', "Modifier un contrevenant");

    yield Permission::new('ROLE_DEFAULT_ASSIGNMENT_RULE_LIST', "Consulter la liste des règles d'assignation par défaut");
    yield Permission::new('ROLE_DEFAULT_ASSIGNMENT_RULE_DETAILS', "Consulter les détails d'une règle d'assignation par défaut");
    yield Permission::new('ROLE_DEFAULT_ASSIGNMENT_RULE_CREATE', "Créer une règle d'assignation par défaut");
    yield Permission::new('ROLE_DEFAULT_ASSIGNMENT_RULE_UPDATE', "Modifier une règle d'assignation par défaut");
};
