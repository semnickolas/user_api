<?php

namespace UserApi\Service;

use Exception;
use UserApi\Object\Request\GetUsers;
use UserApi\Repository\UserRepository;
use UserApi\Exception\UsersNotFoundException;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

/**
 * Class UserService
 * @package UserApi\Service
 */
class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var PaginatedFinderInterface
     */
    private PaginatedFinderInterface $userFinder;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     * @param PaginatedFinderInterface $userFinder
     */
    public function __construct(UserRepository $userRepository, PaginatedFinderInterface $userFinder)
    {
        $this->userRepository = $userRepository;
        $this->userFinder = $userFinder;
    }

    /**
     * @param GetUsers $getUsers
     *
     * @return array
     */
    public function getUsers(GetUsers $getUsers) : array
    {
        //TODO probably need pagination
        try {
            $result = $this->getUsersFromElastic($getUsers);
        } catch (Exception $e) {
            $result = $this->userRepository->getUsers($getUsers->getFilter());
        }

        return $result;
    }

    /**
     * @param GetUsers $getUsers
     *
     * @return array
     * @throws UsersNotFoundException
     */
    private function getUsersFromElastic(GetUsers $getUsers) : array
    {
        $result = $this->userFinder->find($getUsers->getFilter());
        if ($result === []) {
            throw new UsersNotFoundException();
        }

        return $result;
    }
}