<?php

namespace App\Dto\Kobo;

final class KoboAssetSnapshotDto
{
    public ?string $snapshotId = null;

    public ?string $assetId = null;

    public ?string $assetName = null;

    public array $survey = [];

    public array $choices = [];
}

