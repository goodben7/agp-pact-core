<?php

namespace App\Command;

use App\Doctrine\RestrictComplaintByLocationExtension;
use App\Entity\Complaint;
use App\Entity\Location;
use App\Entity\Company;
use App\Entity\Member;
use App\Entity\User;
use App\Model\UserProxyInterface;
use App\Repository\MemberRepository;
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
    name: 'app:test-restrict-complaint-by-location',
    description: 'Test the RestrictComplaintByLocationExtension functionality',
)]
class TestRestrictComplaintByLocationCommand extends Command
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
        $io->title('Testing RestrictComplaintByLocationExtension');

        // Test with no user
        $this->testWithNoUser($io);

        // Test with non-committee user
        $this->testWithNonCommitteeUser($io);

        // Test with committee user without member
        $this->testWithCommitteeUserWithoutMember($io);

        // Test with committee user with member but no company
        $this->testWithCommitteeUserWithMemberNoCompany($io);

        // Test with committee user with member and company but no locations
        $this->testWithCommitteeUserWithMemberCompanyNoLocations($io);

        // Test with committee user with member, company and locations
        $this->testWithCommitteeUserWithMemberCompanyAndLocations($io);
        
        // Test with company user with member, company and locations
        $this->testWithCompanyUserWithMemberCompanyAndLocations($io);
        
        // Test with ngo user with member, company and locations
        $this->testWithNgoUserWithMemberCompanyAndLocations($io);

        $io->success('All tests completed');

        return Command::SUCCESS;
    }

    private function testWithNoUser(SymfonyStyle $io): void
    {
        $io->section('Test with no user');
        
        // Create a mock Security service that returns null for getUser()
        $mockSecurity = $this->createMockSecurity(null);
        
        // Create a mock MemberRepository
        $mockMemberRepository = $this->createMockMemberRepository();
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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

    private function testWithNonCommitteeUser(SymfonyStyle $io): void
    {
        $io->section('Test with non-committee user');
        
        // Create a mock non-committee user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_LAMBDA);
        
        // Create a mock Security service that returns the non-committee user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository
        $mockMemberRepository = $this->createMockMemberRepository();
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since the user is not a committee member
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for non-committee user');
        } else {
            $io->error('Test failed: Query was modified for non-committee user');
        }
    }

    private function testWithCommitteeUserWithoutMember(SymfonyStyle $io): void
    {
        $io->section('Test with committee user without member');
        
        // Create a mock committee user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_COMMITTEE);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock Security service that returns the committee user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns null (no member found)
        $mockMemberRepository = $this->createMockMemberRepository(null);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since there's no member for the user
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for committee user without member');
        } else {
            $io->error('Test failed: Query was modified for committee user without member');
        }
    }

    private function testWithCommitteeUserWithMemberNoCompany(SymfonyStyle $io): void
    {
        $io->section('Test with committee user with member but no company');
        
        // Create a mock committee user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_COMMITTEE);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock member without company
        $member = new Member();
        $member->setUserId('U123');
        $member->setDisplayName('Test Member');
        $member->setActive(true);
        
        // Create a mock Security service that returns the committee user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since the member has no company
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for committee user with member but no company');
        } else {
            $io->error('Test failed: Query was modified for committee user with member but no company');
        }
    }

    private function testWithCommitteeUserWithMemberCompanyNoLocations(SymfonyStyle $io): void
    {
        $io->section('Test with committee user with member and company but no locations');
        
        // Create a mock committee user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_COMMITTEE);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock company with no locations
        $company = new Company();
        $company->setName('Test Company');
        $company->setActive(true);
        
        // Create a mock member with company
        $member = new Member();
        $member->setUserId('U123');
        $member->setDisplayName('Test Member');
        $member->setActive(true);
        $member->setCompany($company);
        
        // Create a mock Security service that returns the committee user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since the company has no locations
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for committee user with member and company but no locations');
        } else {
            $io->error('Test failed: Query was modified for committee user with member and company but no locations');
        }
    }

    private function testWithCommitteeUserWithMemberCompanyAndLocations(SymfonyStyle $io): void
    {
        $io->section('Test with committee user with member, company and locations');
        
        // Create a mock committee user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_COMMITTEE);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create mock locations
        $location1 = new Location();
        $location1->setName('Location 1');
        $location1->setActive(true);
        
        // Set a mock ID for the location
        $reflectionClass = new \ReflectionClass(Location::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($location1, 'L123');
        
        $location2 = new Location();
        $location2->setName('Location 2');
        $location2->setActive(true);
        
        // Set a mock ID for the second location
        $reflectionProperty->setValue($location2, 'L456');
        
        // Create a mock company with locations
        $company = new Company();
        $company->setName('Test Company');
        $company->setActive(true);
        $company->addLocation($location1);
        $company->addLocation($location2);
        
        // Create a mock member with company
        $member = new Member();
        $member->setUserId('U123');
        $member->setDisplayName('Test Member');
        $member->setActive(true);
        $member->setCompany($company);
        
        // Create a mock Security service that returns the committee user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should be modified to include the location restriction
        $hasLocationCondition = str_contains($finalDql, 'location IN (:locationIds)');
        $hasSensitiveCondition = str_contains($finalDql, 'isSensitive = :isSensitive');
        $hasLocationParameter = false;
        $hasSensitiveParameter = false;
        
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === 'locationIds') {
                $locationIds = $parameter->getValue();
                $hasLocationParameter = in_array('L123', $locationIds) && in_array('L456', $locationIds);
            }
            if ($parameter->getName() === 'isSensitive' && $parameter->getValue() === false) {
                $hasSensitiveParameter = true;
            }
        }
        
        if ($hasLocationCondition && $hasSensitiveCondition && $hasLocationParameter && $hasSensitiveParameter) {
            $io->success('Test passed: Query was correctly modified for committee user with member, company and locations');
        } else {
            $io->error('Test failed: Query was not correctly modified for committee user with member, company and locations');
        }
    }
    
    private function testWithCompanyUserWithMemberCompanyAndLocations(SymfonyStyle $io): void
    {
        $io->section('Test with company user with member, company and locations');
        
        // Create a mock company user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_COMPANY);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create mock locations
        $location1 = new Location();
        $location1->setName('Location 1');
        $location1->setActive(true);
        
        // Set a mock ID for the location
        $reflectionClass = new \ReflectionClass(Location::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($location1, 'L123');
        
        $location2 = new Location();
        $location2->setName('Location 2');
        $location2->setActive(true);
        
        // Set a mock ID for the second location
        $reflectionProperty->setValue($location2, 'L456');
        
        // Create a mock company with locations
        $company = new Company();
        $company->setName('Test Company');
        $company->setActive(true);
        $company->addLocation($location1);
        $company->addLocation($location2);
        
        // Create a mock member with company
        $member = new Member();
        $member->setUserId('U123');
        $member->setDisplayName('Test Member');
        $member->setActive(true);
        $member->setCompany($company);
        
        // Create a mock Security service that returns the company user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since the extension only applies to committee users
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for company user');
        } else {
            $io->error('Test failed: Query was modified for company user');
        }
    }
    
    private function testWithNgoUserWithMemberCompanyAndLocations(SymfonyStyle $io): void
    {
        $io->section('Test with NGO user with member, company and locations');
        
        // Create a mock NGO user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_NGO);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create mock locations
        $location1 = new Location();
        $location1->setName('Location 1');
        $location1->setActive(true);
        
        // Set a mock ID for the location
        $reflectionClass = new \ReflectionClass(Location::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($location1, 'L123');
        
        $location2 = new Location();
        $location2->setName('Location 2');
        $location2->setActive(true);
        
        // Set a mock ID for the second location
        $reflectionProperty->setValue($location2, 'L456');
        
        // Create a mock company with locations
        $company = new Company();
        $company->setName('Test Company');
        $company->setActive(true);
        $company->addLocation($location1);
        $company->addLocation($location2);
        
        // Create a mock member with company
        $member = new Member();
        $member->setUserId('U123');
        $member->setDisplayName('Test Member');
        $member->setActive(true);
        $member->setCompany($company);
        
        // Create a mock Security service that returns the NGO user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByLocationExtension($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since the extension only applies to committee users
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for NGO user');
        } else {
            $io->error('Test failed: Query was modified for NGO user');
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

    private function createMockMemberRepository(?Member $member = null): MemberRepository
    {
        return new class($member) extends MemberRepository {
            private ?Member $mockMember;
            
            public function __construct(?Member $mockMember = null)
            {
                $this->mockMember = $mockMember;
            }
            
            public function findOneBy(array $criteria, ?array $orderBy = null): ?Member
            {
                return $this->mockMember;
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