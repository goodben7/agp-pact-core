<?php

namespace App\Command;

use App\Doctrine\RestrictComplaintByLocationExtension;
use App\Entity\Complaint;
use App\Entity\Company;
use App\Entity\Location;
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
        private Security $security,
        private MemberRepository $memberRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Testing RestrictComplaintByLocationExtension');

        // Test with no user
        $this->testWithNoUser($io);

        // Test with committee user without member association
        $this->testWithCommitteeWithoutMember($io);

        // Test with committee user with member but no company
        $this->testWithCommitteeWithMemberNoCompany($io);

        // Test with committee user with member and company but no location
        $this->testWithCommitteeWithMemberCompanyNoLocation($io);

        // Test with committee user with member, company and location
        $this->testWithCommitteeWithMemberCompanyLocation($io);

        // Test with non-committee user
        $this->testWithNonCommitteeUser($io);

        $io->success('All tests completed');

        return Command::SUCCESS;
    }

    private function testWithNoUser(SymfonyStyle $io): void
    {
        $io->section('Test with no user');
        
        // Create a mock Security service that returns null for getUser()
        $mockSecurity = $this->createMockSecurity(null);
        
        // Create a mock MemberRepository
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
        
        // The query should not be modified since there's no user
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified when no user is authenticated');
        } else {
            $io->error('Test failed: Query was modified when no user is authenticated');
        }
    }

    private function testWithCommitteeWithoutMember(SymfonyStyle $io): void
    {
        $io->section('Test with committee user without member association');
        
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
        
        // The query should not be modified since there's no member associated with the user
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for committee user without member association');
        } else {
            $io->error('Test failed: Query was modified for committee user without member association');
        }
    }

    private function testWithCommitteeWithMemberNoCompany(SymfonyStyle $io): void
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

    private function testWithCommitteeWithMemberCompanyNoLocation(SymfonyStyle $io): void
    {
        $io->section('Test with committee user with member and company but no location');
        
        // Create a mock committee user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_COMMITTEE);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock company without location
        $company = new Company();
        $company->setName('Test Company');
        $company->setActive(true);
        
        // Create a mock member with company
        $member = new Member();
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
        
        // The query should not be modified since the company has no location
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for committee user with member and company but no location');
        } else {
            $io->error('Test failed: Query was modified for committee user with member and company but no location');
        }
    }

    private function testWithCommitteeWithMemberCompanyLocation(SymfonyStyle $io): void
    {
        $io->section('Test with committee user with member, company and location');
        
        // Create a mock location
        $location = new Location();
        $location->setName('Test Location');
        $location->setActive(true);
        
        // Set a mock ID for the location
        $reflectionClass = new \ReflectionClass(Location::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($location, 'LC123');
        
        // Create a mock company with location
        $company = new Company();
        $company->setName('Test Company');
        $company->setActive(true);
        
        // Set the location on the company using reflection since we don't have direct access to the setter
        $reflectionClass = new \ReflectionClass(Company::class);
        $reflectionProperty = $reflectionClass->getProperty('location');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($company, $location);
        
        // Create a mock member with company
        $member = new Member();
        $member->setCompany($company);
        
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
        $hasLocationCondition = str_contains($finalDql, 'location = :locationId');
        $hasLocationParameter = false;
        
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === 'locationId' && $parameter->getValue() === 'LC123') {
                $hasLocationParameter = true;
                break;
            }
        }
        
        if ($hasLocationCondition && $hasLocationParameter) {
            $io->success('Test passed: Query was correctly modified for committee user with member, company and location');
        } else {
            $io->error('Test failed: Query was not correctly modified for committee user with member, company and location');
        }
    }

    private function testWithNonCommitteeUser(SymfonyStyle $io): void
    {
        $io->section('Test with non-committee user');
        
        // Create a mock location
        $location = new Location();
        $location->setName('Test Location');
        $location->setActive(true);
        
        // Set a mock ID for the location
        $reflectionClass = new \ReflectionClass(Location::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($location, 'LC123');
        
        // Create a mock company with location
        $company = new Company();
        $company->setName('Test Company');
        $company->setActive(true);
        
        // Set the location on the company using reflection
        $reflectionClass = new \ReflectionClass(Company::class);
        $reflectionProperty = $reflectionClass->getProperty('location');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($company, $location);
        
        // Create a mock member with company
        $member = new Member();
        $member->setCompany($company);
        
        // Create a mock non-committee user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_LAMBDA);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock Security service that returns the non-committee user
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
        
        // The query should not be modified since the user is not a committee member
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for non-committee user');
        } else {
            $io->error('Test failed: Query was modified for non-committee user');
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

    private function createMockMemberRepository(?Member $member): MemberRepository
    {
        return new class($member) extends MemberRepository {
            private ?Member $mockMember;
            
            public function __construct(?Member $mockMember)
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