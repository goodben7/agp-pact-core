<?php

namespace App\Tests\Functional;

use App\Entity\Profile;
use App\Tests\BaseApiTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProfileApiTest extends BaseApiTest
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetCollection(): void
    {
        // Create test data
        $profile = new Profile();
        $profile->setLabel('Test Profile');
        $profile->setPermissions(['ROLE_TEST']);
        $profile->setPersonType('ADM');
        $profile->setActive(true);
        
        $this->em->persist($profile);
        $this->em->flush();

        // Make request
        $response = $this->request('GET', '/profiles', [], [], ['CONTENT_TYPE' => 'application/json']);
        
        // Assert response
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    public function testGetItem(): void
    {
        // Create test data
        $profile = new Profile();
        $profile->setLabel('Test Profile');
        $profile->setPermissions(['ROLE_TEST']);
        $profile->setPersonType('ADM');
        $profile->setActive(true);
        
        $this->em->persist($profile);
        $this->em->flush();

        // Make request
        $response = $this->request('GET', '/profiles/'.$profile->getId(), [], [], ['CONTENT_TYPE' => 'application/json']);
        
        // Assert response
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals($profile->getId(), $data['id']);
        $this->assertEquals('Test Profile', $data['label']);
    }

    public function testPost(): void
    {
        $data = [
            'label' => 'New Profile',
            'permissions' => ['ROLE_TEST'],
            'personType' => 'ADM',
            'active' => true
        ];

        $response = $this->request('POST', '/profiles', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $profileData = json_decode($response->getContent(), true);
        $this->assertEquals('New Profile', $profileData['label']);
    }

    public function testPatch(): void
    {
        // Create test data
        $profile = new Profile();
        $profile->setLabel('Test Profile');
        $profile->setPermissions(['ROLE_TEST']);
        $profile->setPersonType('ADM');
        $profile->setActive(true);
        
        $this->em->persist($profile);
        $this->em->flush();

        // Update data
        $updateData = [
            'label' => 'Updated Profile',
            'active' => false
        ];

        $response = $this->request('PATCH', '/profiles/'.$profile->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($updateData));
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $updatedProfile = json_decode($response->getContent(), true);
        $this->assertEquals('Updated Profile', $updatedProfile['label']);
        $this->assertFalse($updatedProfile['active']);
    }

    public function testInvalidPost(): void
    {
        // Missing required fields
        $invalidData = [
            'label' => '', // Empty label
            'permissions' => [], // Empty permissions
            'personType' => 'INVALID' // Invalid person type
        ];

        $response = $this->request('POST', '/profiles', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($invalidData));
        
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }
}