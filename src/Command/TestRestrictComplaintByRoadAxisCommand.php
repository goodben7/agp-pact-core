<?php

namespace App\Command;

use App\Doctrine\RestrictComplaintByRoadAxisExtension;
use App\Entity\Complaint;
use App\Entity\RoadAxis;
use App\Entity\User;
use App\Model\UserProxyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[AsCommand(
    name: 'app:test-restrict-complaint-by-road-axis',
    description: 'Test the RestrictComplaintByRoadAxisExtension functionality',
)]
class TestRestrictComplaintByRoadAxisCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Testing RestrictComplaintByRoadAxisExtension');

        // Test with no user
        $this->testWithNoUser($io);

        // Test with admin user without road axis
        $this->testWithAdminWithoutRoadAxis($io);

        // Test with admin user with road axis
        $this->testWithAdminWithRoadAxis($io);

        // Test with non-admin user
        $this->testWithNonAdminUser($io);

        $io->success('All tests completed');

        return Command::SUCCESS;
    }

    private function testWithNoUser(SymfonyStyle $io): void
    {
        $io->section('Test with no user');
        
        // Create a mock Security service that returns null for getUser()
        $mockSecurity = $this->createMockSecurity(null);
        
        // Create the extension with the mock security
        $extension = new RestrictComplaintByRoadAxisExtension($mockSecurity);
        
        // Create a query builder
        $queryBuilder = $this->createQueryBuilder();
        $initialDql = $queryBuilder->getDQL();
        
        // Apply the extension
        $extension->applyToCollection(
            $queryBuilder,
            $this->createMockQueryNameGenerator(),
            Complaint::class
        );
        
        // Check if the query was modified
        $finalDql = $queryBuilder->getDQL();
        
        $io->writeln('Initial DQL: ' . $initialDql);
        $io->writeln('Final DQL: ' . $finalDql);
        
        // The query should not be modified since there's no user
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified when no user is authenticated');
        } else {
            $io->error('Test failed: Query was modified when no user is authenticated');
        }
    }

    private function testWithAdminWithoutRoadAxis(SymfonyStyle $io): void
    {
        $io->section('Test with admin user without road axis');
        
        // Create a mock admin user without road axis
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_ADMINISTRATOR_MANAGER);
        
        // Create a mock Security service that returns the admin user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create the extension with the mock security
        $extension = new RestrictComplaintByRoadAxisExtension($mockSecurity);
        
        // Create a query builder
        $queryBuilder = $this->createQueryBuilder();
        $initialDql = $queryBuilder->getDQL();
        
        // Apply the extension
        $extension->applyToCollection(
            $queryBuilder,
            $this->createMockQueryNameGenerator(),
            Complaint::class
        );
        
        // Check if the query was modified
        $finalDql = $queryBuilder->getDQL();
        
        $io->writeln('Initial DQL: ' . $initialDql);
        $io->writeln('Final DQL: ' . $finalDql);
        
        // The query should not be modified since the admin has no road axis
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for admin without road axis');
        } else {
            $io->error('Test failed: Query was modified for admin without road axis');
        }
    }

    private function testWithAdminWithRoadAxis(SymfonyStyle $io): void
    {
        $io->section('Test with admin user with road axis');
        
        // Create a mock road axis
        $roadAxis = new RoadAxis();
        $roadAxis->setName('Test Road Axis');
        $roadAxis->setActive(true);
        
        // Set a mock ID for the road axis
        $reflectionClass = new \ReflectionClass(RoadAxis::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($roadAxis, 'RA123');
        
        // Create a mock admin user with road axis
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_ADMINISTRATOR_MANAGER);
        $user->setRoadAxis($roadAxis);
        
        // Create a mock Security service that returns the admin user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create the extension with the mock security
        $extension = new RestrictComplaintByRoadAxisExtension($mockSecurity);
        
        // Create a query builder
        $queryBuilder = $this->createQueryBuilder();
        $initialDql = $queryBuilder->getDQL();
        
        // Apply the extension
        $extension->applyToCollection(
            $queryBuilder,
            $this->createMockQueryNameGenerator(),
            Complaint::class
        );
        
        // Check if the query was modified
        $finalDql = $queryBuilder->getDQL();
        $parameters = $queryBuilder->getParameters();
        
        $io->writeln('Initial DQL: ' . $initialDql);
        $io->writeln('Final DQL: ' . $finalDql);
        
        // The query should be modified to include the road axis restriction
        $hasRoadAxisCondition = str_contains($finalDql, 'roadAxis = :roadAxisId');
        $hasRoadAxisParameter = false;
        
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === 'roadAxisId' && $parameter->getValue() === 'RA123') {
                $hasRoadAxisParameter = true;
                break;
            }
        }
        
        if ($hasRoadAxisCondition && $hasRoadAxisParameter) {
            $io->success('Test passed: Query was correctly modified for admin with road axis');
        } else {
            $io->error('Test failed: Query was not correctly modified for admin with road axis');
        }
    }

    private function testWithNonAdminUser(SymfonyStyle $io): void
    {
        $io->section('Test with non-admin user');
        
        // Create a mock road axis
        $roadAxis = new RoadAxis();
        $roadAxis->setName('Test Road Axis');
        $roadAxis->setActive(true);
        
        // Set a mock ID for the road axis
        $reflectionClass = new \ReflectionClass(RoadAxis::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($roadAxis, 'RA123');
        
        // Create a mock non-admin user with road axis
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_LAMBDA);
        $user->setRoadAxis($roadAxis);
        
        // Create a mock Security service that returns the non-admin user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create the extension with the mock security
        $extension = new RestrictComplaintByRoadAxisExtension($mockSecurity);
        
        // Create a query builder
        $queryBuilder = $this->createQueryBuilder();
        $initialDql = $queryBuilder->getDQL();
        
        // Apply the extension
        $extension->applyToCollection(
            $queryBuilder,
            $this->createMockQueryNameGenerator(),
            Complaint::class
        );
        
        // Check if the query was modified
        $finalDql = $queryBuilder->getDQL();
        
        $io->writeln('Initial DQL: ' . $initialDql);
        $io->writeln('Final DQL: ' . $finalDql);
        
        // The query should not be modified since the user is not an admin
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for non-admin user');
        } else {
            $io->error('Test failed: Query was modified for non-admin user');
        }
    }

    private function createQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c')
            ->from(Complaint::class, 'c');
        
        return $queryBuilder;
    }

    private function createMockSecurity(?User $user): Security
    {
        return new class($user) extends Security {
            private ?User $mockUser;
            
            public function __construct(?User $mockUser)
            {
                $this->mockUser = $mockUser;
            }
            
            public function getUser(): ?User
            {
                return $this->mockUser;
            }
        };
    }

    private function createMockQueryNameGenerator(): QueryNameGeneratorInterface
    {
        return new class() implements QueryNameGeneratorInterface {
            public function generateJoinAlias(string $association): string
            {
                return $association . '_alias';
            }
            
            public function generateParameterName(string $name): string
            {
                return $name . '_param';
            }
        };
    }
}