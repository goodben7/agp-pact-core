<?php

namespace App\Service;

use App\Contract\NotificationSenderInterface;
use App\Entity\Notification;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class SmsNotificationSender implements NotificationSenderInterface
{
    private const KECCEL_API_URL = 'https://sms.keccel.com/api/v2/sms';

    public function __construct(
        private HttpClientInterface $client,
        private string              $keccelApiToken,
        private string              $keccelSender,
        private int                 $keccelCampaignId,
        private int                 $keccelRouteId
    ) {
    }

    public function send(Notification $notification): void
    {
        $payload = [
            'campaignId' => $this->keccelCampaignId,
            'routeId' => $this->keccelRouteId,
            'sender' => $this->keccelSender,
            'mode' => 'text',
            'message' => $notification->getBody(),
            'contacts' => [
                [
                    'mobile' => $this->formatPhoneNumber($notification->getRecipient()),
                    'parameters' => [] 
                ]
            ],
        ];

        try {
            $response = $this->client->request('POST', self::KECCEL_API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->keccelApiToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            if ($response->getStatusCode() !== 200) {
                $content = $response->getContent(false);
                throw new \RuntimeException('Échec d’envoi SMS via Keccel: ' . $content);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur Keccel: ' . $e->getMessage());
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        return preg_replace('/[^\d]/', '', $phone);
    }

    public function support(string $sentVia): bool
    {
        return $sentVia === Notification::SENT_VIA_SMS;
    }
}
