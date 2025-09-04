<?php

namespace App\Command;

use App\Doctrine\RestrictComplaintByRoadAxisExtensionForNgo;
use App\Entity\Complaint;
use App\Entity\Company;
use App\Entity\Member;
use App\Entity\RoadAxis;
use App\Entity\User;
use App\Model\UserProxyInterface;
use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
    name: 'app:test-restrict-complaint-by-road-axis-for-ngo',
    description: 'Test the RestrictComplaintByRoadAxisExtensionForNgo functionality',
)]
class TestRestrictComplaintByRoadAxisExtensionForNgoCommand extends Command
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
        $io->title('Testing RestrictComplaintByRoadAxisExtensionForNgo');

        // Test with no user
        $this->testWithNoUser($io);

        // Test with NGO user without member association
        $this->testWithNgoWithoutMember($io);

        // Test with NGO user with member but no company
        $this->testWithNgoWithMemberNoCompany($io);

        // Test with NGO user with member and company but no road axes
        $this->testWithNgoWithMemberCompanyNoRoadAxes($io);

        // Test with NGO user with member, company and road axes
        $this->testWithNgoWithMemberCompanyRoadAxes($io);

        // Test with non-NGO user
        $this->testWithNonNgoUser($io);

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
        $extension = new RestrictComplaintByRoadAxisExtensionForNgo($mockSecurity, $mockMemberRepository);
        
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

    private function testWithNgoWithoutMember(SymfonyStyle $io): void
    {
        $io->section('Test with NGO user without member association');
        
        // Create a mock NGO user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_NGO);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock Security service that returns the NGO user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns null (no member found)
        $mockMemberRepository = $this->createMockMemberRepository(null);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByRoadAxisExtensionForNgo($mockSecurity, $mockMemberRepository);
        
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
            $io->success('Test passed: Query was not modified for NGO user without member association');
        } else {
            $io->error('Test failed: Query was modified for NGO user without member association');
        }
    }

    private function testWithNgoWithMemberNoCompany(SymfonyStyle $io): void
    {
        $io->section('Test with NGO user with member but no company');
        
        // Create a mock NGO user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_NGO);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock member without company
        $member = new Member();
        
        // Create a mock Security service that returns the NGO user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByRoadAxisExtensionForNgo($mockSecurity, $mockMemberRepository);
        
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
            $io->success('Test passed: Query was not modified for NGO user with member but no company');
        } else {
            $io->error('Test failed: Query was modified for NGO user with member but no company');
        }
    }

    private function testWithNgoWithMemberCompanyNoRoadAxes(SymfonyStyle $io): void
    {
        $io->section('Test with NGO user with member and company but no road axes');
        
        // Create a mock NGO user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_NGO);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock company with empty road axes collection
        $company = new Company();
        $reflectionClass = new \ReflectionClass(Company::class);
        $reflectionProperty = $reflectionClass->getProperty('roadAxes');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($company, new ArrayCollection());
        
        // Create a mock member with company
        $member = new Member();
        $member->setCompany($company);
        
        // Create a mock Security service that returns the NGO user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByRoadAxisExtensionForNgo($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since the company has no road axes
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for NGO user with member and company but no road axes');
        } else {
            $io->error('Test failed: Query was modified for NGO user with member and company but no road axes');
        }
    }

    private function testWithNgoWithMemberCompanyRoadAxes(SymfonyStyle $io): void
    {
        $io->section('Test with NGO user with member, company and road axes');
        
        // Create mock road axes
        $roadAxis1 = new RoadAxis();
        $roadAxis1->setName('Test Road Axis 1');
        $roadAxis1->setActive(true);
        
        $roadAxis2 = new RoadAxis();
        $roadAxis2->setName('Test Road Axis 2');
        $roadAxis2->setActive(true);
        
        // Set mock IDs for the road axes
        $reflectionClass = new \ReflectionClass(RoadAxis::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($roadAxis1, 'RA123');
        $reflectionProperty->setValue($roadAxis2, 'RA456');
        
        // Create a mock company with road axes
        $company = new Company();
        $roadAxes = new ArrayCollection([$roadAxis1, $roadAxis2]);
        $reflectionClass = new \ReflectionClass(Company::class);
        $reflectionProperty = $reflectionClass->getProperty('roadAxes');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($company, $roadAxes);
        
        // Create a mock NGO user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_NGO);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock member with company
        $member = new Member();
        $member->setCompany($company);
        
        // Create a mock Security service that returns the NGO user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository that returns the member
        $mockMemberRepository = $this->createMockMemberRepository($member);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByRoadAxisExtensionForNgo($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should be modified to include the road axes restriction
        $hasRoadAxisCondition = str_contains($finalDql, 'roadAxis IN (:roadAxisIds)');
        $hasRoadAxisParameter = false;
        
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === 'roadAxisIds') {
                $paramValue = $parameter->getValue();
                $hasRoadAxisParameter = is_array($paramValue) && 
                                       in_array('RA123', $paramValue) && 
                                       in_array('RA456', $paramValue);
                break;
            }
        }
        
        if ($hasRoadAxisCondition && $hasRoadAxisParameter) {
            $io->success('Test passed: Query was correctly modified for NGO user with member, company and road axes');
        } else {
            $io->error('Test failed: Query was not correctly modified for NGO user with member, company and road axes');
        }
    }

    private function testWithNonNgoUser(SymfonyStyle $io): void
    {
        $io->section('Test with non-NGO user');
        
        // Create a mock non-NGO user
        $user = new User();
        $user->setPersonType(UserProxyInterface::PERSON_LAMBDA);
        
        // Set a mock ID for the user
        $reflectionClass = new \ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, 'U123');
        
        // Create a mock Security service that returns the non-NGO user
        $mockSecurity = $this->createMockSecurity($user);
        
        // Create a mock MemberRepository
        $mockMemberRepository = $this->createMockMemberRepository(null);
        
        // Create the extension with the mock security and repository
        $extension = new RestrictComplaintByRoadAxisExtensionForNgo($mockSecurity, $mockMemberRepository);
        
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
        
        // The query should not be modified since the user is not an NGO
        if ($initialDql === $finalDql) {
            $io->success('Test passed: Query was not modified for non-NGO user');
        } else {
            $io->error('Test failed: Query was modified for non-NGO user');
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