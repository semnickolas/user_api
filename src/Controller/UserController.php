<?php

namespace UserApi\Controller;

use UserApi\Object\Request\GetUsers;
use UserApi\Enum\ResponseEnum;
use UserApi\Object\Request\CreateUser;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class UserController
 * @package UserApi\Controller
 * @Rest\Route("/user")
 */
class UserController extends AbstractFOSRestController
{
    use HandleTrait;

    /**
     * UserController constructor.
     * @param MessageBusInterface $messageBus
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Rest\Put("/api/create")
     * @ParamConverter(
     *     "request",
     *     class="UserApi\Object\Request\CreateUser",
     *     converter="fos_rest.request_body"
     * )
     * @param CreateUser $request
     * @return Response
     */
    public function createUser(CreateUser $request): Response
    {
        $this->messageBus->dispatch($request);

        return $this->handleView($this->view(ResponseEnum::USER_CREATED));
    }

    /**

     * @Rest\Get("/users", name="users-page")
     * @ParamConverter(
     *     "request",
     *     class="UserApi\Object\Request\GetUsers",
     *     converter="resource.query.param_converter",
     *     options={"enableRouteAttributes"=true}
     * )
     * @param GetUsers $request
     * @return Response
     */
    public function getUsers(GetUsers $request): Response
    {
        $response = $this->render(
            ResponseEnum::USERS_VIEW,
            [
                ResponseEnum::USERS_INDEX => $this->handle($request),
                ResponseEnum::FILTER_INDEX => $request->getFilter(),
            ]
        );
        $response->headers->add(ResponseEnum::HTML_HEADER);

        return $response;
    }
}