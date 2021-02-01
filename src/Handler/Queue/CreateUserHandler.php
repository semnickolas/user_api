<?php

namespace UserApi\Handler\Queue;

use UserApi\Entity\User;
use Doctrine\ORM\ORMException;
use UserApi\Enum\SerializerEnum;
use UserApi\Object\Request\CreateUser;
use UserApi\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\OptimisticLockException;
use UserApi\Validation\SimpleObjectValidator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class CreateUserHandler
 * @package UserApi\Handler\Queue
 */
class CreateUserHandler implements MessageHandlerInterface
{
    private UserRepository $repository;

    private SerializerInterface $serializer;

    private SimpleObjectValidator $validator;

    /**
     * CreateUserHandler constructor.
     *
     * @param UserRepository $repository
     * @param SerializerInterface $serializer
     * @param SimpleObjectValidator $validator
     */
    public function __construct(
        UserRepository $repository,
        SerializerInterface $serializer,
        SimpleObjectValidator $validator
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param CreateUser $createUser
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(CreateUser $createUser) : void
    {
        //TODO : probably need db replication
        $user = $this->serializeUser($createUser);
        $this->validator->validate($user);
        $this->repository->saveUser($user);
    }

    /**
     * @param CreateUser $createUser
     *
     * @return User
     */
    private function serializeUser(CreateUser $createUser) : User
    {
        return $this->serializer->deserialize(
            $this->serializer->serialize($createUser, SerializerEnum::FORMAT_JSON),
            User::class,
            SerializerEnum::FORMAT_JSON
        );
    }
}