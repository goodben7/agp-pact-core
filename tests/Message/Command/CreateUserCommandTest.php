<?php

namespace App\Tests\Message\Command;

use App\Entity\Profile;
use App\Message\Command\CreateUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandTest extends TestCase
{
    private ValidatorInterface $validator;
    private Profile $profile;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()  
            ->getValidator();
            
        $this->profile = new Profile();
    }

    public function testCreateValidCommand(): void
    {
        $command = new CreateUserCommand(
            plainPassword: 'password123',
            profile: $this->profile,
            email: 'test@example.com',
            phone: '+33612345678',
            displayName: 'Test User'
        );

        $violations = $this->validator->validate($command);
        
        $this->assertCount(0, $violations);
        $this->assertEquals('password123', $command->plainPassword);
        $this->assertEquals($this->profile, $command->profile);
        $this->assertEquals('test@example.com', $command->email);
        $this->assertEquals('+33612345678', $command->phone);
        $this->assertEquals('Test User', $command->displayName);
    }

    public function testCreateCommandWithoutOptionalFields(): void
    {
        $command = new CreateUserCommand(
            plainPassword: 'password123',
            profile: $this->profile
        );

        $violations = $this->validator->validate($command);
        
        $this->assertCount(0, $violations);
        $this->assertNull($command->email);
        $this->assertNull($command->phone);
        $this->assertNull($command->displayName);
    }

    public function testCreateCommandWithInvalidEmail(): void
    {
        $command = new CreateUserCommand(
            plainPassword: 'password123',
            profile: $this->profile,
            email: 'invalid-email'
        );

        $violations = $this->validator->validate($command);
        
        $this->assertCount(1, $violations);
        $this->assertEquals('email', $violations[0]->getPropertyPath());
    }

    public function testCreateCommandWithoutRequiredFields(): void
    {
        $command = new CreateUserCommand();

        $violations = $this->validator->validate($command);
        
        $this->assertCount(2, $violations);
        $violatedProperties = array_map(
            fn($violation) => $violation->getPropertyPath(),
            iterator_to_array($violations)
        );
        
        $this->assertContains('plainPassword', $violatedProperties);
        $this->assertContains('profile', $violatedProperties);
    }
}