<?php

declare(strict_types=1);

use App\Model\TriggerEvent;

return static function (): iterable {
    yield TriggerEvent::new('App\Message\UserRegisteredMessage', "Utilisateur enregistré");
    yield TriggerEvent::new('App\Message\ComplaintRegisteredMessage', "Plainte enregistrée");
    yield TriggerEvent::new('App\Message\ComplainantDecisionMessage', "Décision prise par le plaignant");
    yield TriggerEvent::new('App\Message\ComplaintClassifiedAssignedMessage', "Plainte classifiée et assignée");
    yield TriggerEvent::new('App\Message\ComplaintClosedMessage', "Plainte clôturée");
    yield TriggerEvent::new('App\Message\ComplaintWorkflowMessage', "Mise à jour du workflow de la plainte");
    yield TriggerEvent::new('App\Message\ComplaintEscalatedMessage', "Plainte escaladée");
    yield TriggerEvent::new('App\Message\ComplaintReceivabilityVerifiedMessage', "Recevabilité de la plainte vérifiée");
    yield TriggerEvent::new('App\Message\InternalDecisionMadeMessage', "Décision interne prise");
    yield TriggerEvent::new('App\Message\MeritsExaminedMessage', "Mérites de la plainte examinés");
    yield TriggerEvent::new('App\Message\ResolutionExecutedMessage', "Résolution exécutée");
    yield TriggerEvent::new('App\Message\ResolutionProposedMessage', "Résolution proposée");
    yield TriggerEvent::new('App\Message\SatisfactionFollowedUpMessage', "Suivi de la satisfaction effectué");
};

