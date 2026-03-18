<?php

namespace App\Dto\ParV2;

final class SyncParV2RequestDto
{
    public ?string $assetId = null;

    public int $limit = 50;

    public int $start = 0;

    public ?int $maxResults = null;
}

