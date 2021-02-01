<?php

namespace App\Tests\unit\Service;

use UserApi\Entity\User;
use PHPUnit\Framework\TestCase;
use UserApi\Service\UserService;
use UserApi\Object\Request\GetUsers;
use UserApi\Repository\UserRepository;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Class UserServiceTest
 * @package App\Tests\unit\Service
 */
class UserServiceTest extends TestCase
{
    private UserService $userService;

    private UserRepository $userRepository;

    private PaginatedFinderInterface $userFinder;

    private ObjectNormalizer $normalizer;

    /**
     * UserServiceTest constructor.
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->normalizer = new ObjectNormalizer();
    }

    public function setUp()
    {
        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userFinder = $this->getMockBuilder(PaginatedFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userService = new UserService($this->userRepository, $this->userFinder);
    }

    /**
     * @dataProvider successUsersData
     * @param GetUsers $query
     * @param array $users
     * @param array $esUsers
     * @param array $expectedResult
     */
    public function testGetUsersSuccess(
        GetUsers $query,
        array $users,
        array $esUsers,
        array $expectedResult
    ) : void {
        $this->userRepository->method('getUsers')->with($query->getFilter())->willReturn($users);
        $this->userFinder->method('find')->with($query->getFilter())->willReturn($esUsers);
        $result = $this->userService->getUsers($query);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array[]
     * @throws ExceptionInterface
     */
    public function successUsersData() : array
    {
        $query1 = $this->normalizer->denormalize(
            ['filter' => ''],
            GetUsers::class
        );
        $query2 = $this->normalizer->denormalize(
            ['filter' => 'aleksey'],
            GetUsers::class
        );

        $user = $this->normalizer->denormalize(
            ['firstName' => 'Aleksey', 'lastName' => 'Ivanov'],
            User::class
        );

        return [
            'success test case with 0 users from es' => [$query1, [$user], [], [$user]],
            'success test case with users from es' => [$query2, [], [$user], [$user]],
        ];
    }
}