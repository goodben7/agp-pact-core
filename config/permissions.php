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
    yield Permission::new('ROLE_GENERAL_PARAMETER_UPDATE', "Modifier un paramètre général");

    yield Permission::new('ROLE_COMPLAINT_LIST', "Consulter la liste des plaintes");
    yield Permission::new('ROLE_COMPLAINT_DETAILS', "Consulter les détails d'une plainte");
    yield Permission::new('ROLE_COMPLAINT_CREATE', "Créer une plainte");
    yield Permission::new('ROLE_COMPLAINT_UPDATE', "Modifier une plainte");

    yield Permission::new('ROLE_COMPLAINANT_LIST', 'Consulter la liste des plaintants');
    yield Permission::new('ROLE_COMPLAINANT_DETAILS', 'Consulter les détails d\'un plaintant');
    yield Permission::new('ROLE_COMPLAINANT_CREATE', 'Créer un plaintant');
    yield Permission::new('ROLE_COMPLAINANT_UPDATE', 'Modifier un plaintant');

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

    yield Permission::new('ROLE_SPECIES_PRICE_LIST', 'Consulter la liste des prix des espèces');
    yield Permission::new('ROLE_SPECIES_PRICE_DETAILS', 'Consulter les détails d\'un prix d\'espèce');
    yield Permission::new('ROLE_SPECIES_PRICE_CREATE', 'Créer un prix d\'espèce');
    yield Permission::new('ROLE_SPECIES_PRICE_UPDATE', 'Modifier un prix d\'espèce');

    yield Permission::new('ROLE_VICTIM_LIST', 'Consulter la liste des victimes');
    yield Permission::new('ROLE_VICTIM_DETAILS', 'Consulter les détails d\'une victime');
    yield Permission::new('ROLE_VICTIM_CREATE', 'Créer une victime');
    yield Permission::new('ROLE_VICTIM_UPDATE', 'Modifier une victime');

    yield Permission::new('ROLE_WORKFLOW_STEP_LIST', 'Consulter la liste des étapes de workflow');
    yield Permission::new('ROLE_WORKFLOW_STEP_DETAILS', 'Consulter les détails d\'une étape de workflow');
    yield Permission::new('ROLE_WORKFLOW_STEP_CREATE', 'Créer une étape de workflow');
    yield Permission::new('ROLE_WORKFLOW_STEP_UPDATE', 'Modifier une étape de workflow');

    yield Permission::new('ROLE_WORKFLOW_ACTION_LIST', 'Consulter la liste des actions de workflow');
    yield Permission::new('ROLE_WORKFLOW_ACTION_DETAILS', 'Consulter les détails d\'une action de workflow');
    yield Permission::new('ROLE_WORKFLOW_ACTION_CREATE', 'Créer une action de workflow');
    yield Permission::new('ROLE_WORKFLOW_ACTION_UPDATE', 'Modifier une action de workflow');

    yield Permission::new('ROLE_WORKFLOW_TRANSITION_LIST', 'Consulter la liste des transitions de workflow');
    yield Permission::new('ROLE_WORKFLOW_TRANSITION_DETAILS', 'Consulter les détails d\'une transition de workflow');
    yield Permission::new('ROLE_WORKFLOW_TRANSITION_CREATE', 'Créer une transition de workflow');
    yield Permission::new('ROLE_WORKFLOW_TRANSITION_UPDATE', 'Modifier une transition de workflow');
};
