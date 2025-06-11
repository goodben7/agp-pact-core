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

    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_LIST', "Consulter la liste des modèles de notification");
    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_VIEW', "Consulter les détails d'un modèle de notification");
    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_CREATE', "Créer un modèle de notification");
    yield Permission::new('ROLE_NOTIFICATION_TEMPLATE_UPDATE', "Modifier un modèle de notification");

};
