<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class BaseApiTest extends WebTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    protected function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null
    ): Response {
        $client = static::createClient();
        $client->request($method, $uri, $parameters, $files, $server, $content);
        
        return $client->getResponse();
    }
}