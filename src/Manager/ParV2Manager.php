<?php

namespace App\Manager;

use App\Entity\ParV2;
use App\Repository\ParV2Repository;
use App\Model\NewParV2Model;
use App\Service\Kobo\KoboApiClient;
use Doctrine\ORM\EntityManagerInterface;

final class ParV2Manager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ParV2Repository $repository,
        private KoboApiClient $kobo
    )
    {
    }

    public function getKoboAssetSnapshots(): array
    {
        return $this->kobo->getAssetSnapshots();
    }

    public function normalizeAssetId(string $assetIdOrUrl): string
    {
        return $this->kobo->normalizeAssetId($assetIdOrUrl);
    }

    public function getKoboFormStructureFromSnapshot(array $snapshot): array
    {
        $source = $snapshot['source'] ?? [];
        if (!is_array($source)) {
            $source = [];
        }

        $survey = $source['survey'] ?? [];
        $choices = $source['choices'] ?? [];

        if (!is_array($survey)) {
            $survey = [];
        }
        if (!is_array($choices)) {
            $choices = [];
        }

        return [
            'survey' => $survey,
            'choices' => $choices,
        ];
    }

    public function syncFromKoboAsset(string $assetId, int $limit = 50, int $start = 0, ?int $maxResults = null): array
    {
        $created = 0;
        $updated = 0;
        $processed = 0;

        $page = $this->kobo->getAssetSubmissionsPage($assetId, $limit, $start);

        while (true) {
            $results = $page['results'] ?? [];
            if (!is_array($results)) {
                $results = [];
            }

            foreach ($results as $payload) {
                if (!is_array($payload)) {
                    continue;
                }

                $koboId = $payload['_id'] ?? null;
                $koboId = is_int($koboId) || is_string($koboId) ? (string) $koboId : null;
                if ($koboId === null || $koboId === '') {
                    continue;
                }

                $exists = $this->repository->findOneBy(['koboId' => $koboId]) !== null;
                $this->create(NewParV2Model::fromKoboPayload($payload, $koboId));

                $processed++;
                if ($exists) {
                    $updated++;
                } else {
                    $created++;
                }

                if ($maxResults !== null && $processed >= $maxResults) {
                    return ['processed' => $processed, 'created' => $created, 'updated' => $updated];
                }
            }

            $next = $page['next'] ?? null;
            if (!is_string($next) || $next === '') {
                break;
            }

            $page = $this->kobo->getByUrl($next);
        }

        return ['processed' => $processed, 'created' => $created, 'updated' => $updated];
    }

    public function create(NewParV2Model $model): ParV2
    {
        $existing = $this->repository->findOneBy(['koboId' => $model->koboId]);

        $parV2 = $existing ?? new ParV2();

        $parV2->setKoboId($model->koboId);
        $parV2->setFormhubUuid($model->formhubUuid);
        $parV2->setStartAt($model->startAt);
        $parV2->setEndAt($model->endAt);
        $parV2->setProvince($model->province);
        $parV2->setAxeRoutier($model->axeRoutier);
        $parV2->setLieuActifAffecte($model->lieuActifAffecte);
        $parV2->setNomPersonneInterview($model->nomPersonneInterview);
        $parV2->setQualitePersInterviewe($model->qualitePersInterviewe);
        $parV2->setDateInventaire($model->dateInventaire);
        $parV2->setGpsActif($model->gpsActif);
        $parV2->setNomChefMenageCm($model->nomChefMenageCm);
        $parV2->setSexeChefMenage($model->sexeChefMenage);
        $parV2->setAgeChefMenage($model->ageChefMenage);
        $parV2->setDegreVulnerabiliteChefMenage($model->degreVulnerabiliteChefMenage);
        $parV2->setEtatCivilChefMenage($model->etatCivilChefMenage);
        $parV2->setNombreCompositionMenage($model->nombreCompositionMenage);
        $parV2->setGroupFx3uw05($model->groupFx3uw05);
        $parV2->setGroupCd71m48($model->groupCd71m48);
        $parV2->setSourceRevenue($model->sourceRevenue);
        $parV2->setSourceEnergieLumiere($model->sourceEnergieLumiere);
        $parV2->setSourceEnergieCuisine($model->sourceEnergieCuisine);
        $parV2->setSourceEauPotable($model->sourceEauPotable);
        $parV2->setTypeActifAffect($model->typeActifAffect);
        $parV2->setGroupXh1rg07($model->groupXh1rg07);
        $parV2->setEtesVousInformeRelocalis($model->etesVousInformeRelocalis);
        $parV2->setAcceptezRelocalis($model->acceptezRelocalis);
        $parV2->setConditionRelocalis($model->conditionRelocalis);
        $parV2->setVoulezVousLaisserQuelqu($model->voulezVousLaisserQuelqu);
        $parV2->setBonneChoixRelocaliser($model->bonneChoixRelocaliser);
        $parV2->setEnCasProbleme($model->enCasProbleme);
        $parV2->setPhoto1($model->photo1);
        $parV2->setPhoto2($model->photo2);
        $parV2->setNomEnqueteur($model->nomEnqueteur);
        $parV2->setDureeInterviewMinutes($model->dureeInterviewMinutes);
        $parV2->setKoboVersion($model->koboVersion);
        $parV2->setMetaInstanceId($model->metaInstanceId);
        $parV2->setMetaDeprecatedId($model->metaDeprecatedId);
        $parV2->setXformIdString($model->xformIdString);
        $parV2->setKoboUuid($model->koboUuid);
        $parV2->setAttachments($model->attachments);
        $parV2->setKoboStatus($model->koboStatus);
        $parV2->setGeolocation($model->geolocation);
        $parV2->setSubmissionTime($model->submissionTime);
        $parV2->setTags($model->tags);
        $parV2->setNotes($model->notes);
        $parV2->setValidationStatus($model->validationStatus);
        $parV2->setSubmittedBy($model->submittedBy);
        $parV2->setMetaRootUuid($model->metaRootUuid);
        $parV2->setRawPayload($model->rawPayload);

        $this->em->persist($parV2);
        $this->em->flush();

        return $parV2;
    }
}
