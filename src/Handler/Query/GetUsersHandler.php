<?php

namespace UserApi\Handler\Query;

use UserApi\Service\UserService;
use UserApi\Object\Request\GetUsers;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class GetUsersHandler
 * @package UserApi\Handler\Query
 */
class GetUsersHandler implements MessageHandlerInterface
{
    private UserService $userService;

    /**
     * GetUsersHandler constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param GetUsers $getUsers
     *
     * @return array
     */
    public function __invoke(GetUsers $getUsers) : array
    {
        return $this->userService->getUsers($getUsers);
    }
}