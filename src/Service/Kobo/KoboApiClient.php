<?php

namespace App\Service\Kobo;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class KoboApiClient
{
    public function __construct(
        private HttpClientInterface $client,
        private string $token,
        private string $baseUri = 'https://kf.kobotoolbox.org'
    )
    {
        if ($this->token === '') {
            throw new \RuntimeException('KOBO_API_TOKEN is not set');
        }
    }

    public function getAssetSnapshots(): array
    {
        return $this->get('/api/v2/asset_snapshots/');
    }

    public function getAssetSubmissionsPage(string $assetId, int $limit = 50, int $start = 0): array
    {
        $assetId = $this->normalizeAssetId($assetId);

        return $this->get(sprintf('/api/v2/assets/%s/data/', $assetId), [
            'limit' => $limit,
            'start' => $start,
        ]);
    }

    public function getByUrl(string $url): array
    {
        return $this->get($url);
    }

    private function get(string $pathOrUrl, array $query = []): array
    {
        $url = str_starts_with($pathOrUrl, 'http://') || str_starts_with($pathOrUrl, 'https://')
            ? $pathOrUrl
            : rtrim($this->baseUri, '/') . $pathOrUrl;

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Token ' . $this->token,
                'Accept' => 'application/json',
            ],
            'query' => $query,
        ]);

        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException(sprintf('Kobo API error (%d): %s', $status, $response->getContent(false)));
        }

        return $response->toArray(false);
    }

    public function normalizeAssetId(string $assetIdOrUrl): string
    {
        $assetIdOrUrl = trim($assetIdOrUrl);
        if ($assetIdOrUrl === '') {
            throw new \InvalidArgumentException('assetId is required');
        }

        if (str_starts_with($assetIdOrUrl, 'http://') || str_starts_with($assetIdOrUrl, 'https://')) {
            $path = parse_url($assetIdOrUrl, PHP_URL_PATH);
            if (is_string($path)) {
                $parts = array_values(array_filter(explode('/', trim($path, '/')), static fn ($p) => $p !== ''));
                $assetsIndex = array_search('assets', $parts, true);
                if (is_int($assetsIndex) && isset($parts[$assetsIndex + 1])) {
                    return $parts[$assetsIndex + 1];
                }
            }
        }

        return rtrim($assetIdOrUrl, '/');
    }
}
