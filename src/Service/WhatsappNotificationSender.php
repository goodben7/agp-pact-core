<?php

namespace App\Service;

use App\Contract\NotificationSenderInterface;
use App\Entity\Notification;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class WhatsappNotificationSender implements NotificationSenderInterface
{
    private const API_URL = 'https://api.ultramsg.com/%s/messages/chat';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface     $logger,
        private string              $instanceId,
        private string              $token
    )
    {
    }

    public function send(Notification $notification): void
    {
        $params = [
            'token' => $this->token,
            'to' => $this->formatPhoneNumber($notification->getRecipient()),
            'body' => $notification->getBody()
        ];

        try {
            $this->logger->info(sprintf(self::API_URL, $this->instanceId));

            $this->httpClient->request('POST',
                sprintf(self::API_URL, $this->instanceId),
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    'body' => http_build_query($params),
                    'verify_peer' => false,
                    'verify_host' => false
                ]
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur Whatsapp via UltraMsg : ' . $e->getMessage());
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        return preg_replace('/[^\d]/', '', $phone);
    }

    public function support(string $sentVia): bool
    {
        return $sentVia === Notification::SENT_VIA_WHATSAPP;
    }
}
