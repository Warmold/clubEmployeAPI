<?php

namespace App\Controller;

use App\Assembler\User\UserAssembler;
use App\Manager\User\UserManager;
use App\Serializer\JsonSerializer;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    /**
     * @var UserManager
     */
    public UserManager $userManager;

    /**
     * @var UserAssembler
     */
    public UserAssembler $userAssembler;

    /**
     * @var JsonSerializer
     */
    public JsonSerializer $serializer;

    /**
     * InvoiceController constructor.
     *
     * @param UserManager $userManager
     * @param UserAssembler $userAssembler
     * @param ValidatorInterface $validator
     * @param JsonSerializer $serializer
     */
    public function __construct(
        UserManager $userManager,
        UserAssembler $userAssembler,
        JsonSerializer $serializer
    )
    {
        $this->userManager = $userManager;
        $this->userAssembler = $userAssembler;
        $this->serializer = $serializer;
    }

    /**
     * @Route("users", name="get_users", methods={"GET"})
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1")
     * @Rest\QueryParam(name="per_page", requirements="5|10|20|50", default="10")
     * @Rest\QueryParam(name="order_by", requirements="name|reference|categoryDefault.name|enabled|completed")
     * @Rest\QueryParam(name="order_direction", requirements="(asc|desc)", default="asc")
     * @Rest\QueryParam(name="filters", map=true)
     * @Rest\QueryParam(name="search", requirements="\w+")
     *
     * @return Response
     *
     * @throws \ReflectionException
     */
    public function getUsers(ParamFetcherInterface $paramFetcher): Response
    {
        return new Response(
            $this->serializer->serialize(
                $this->userAssembler->transformPagerfanta(
                    $this->userManager->getPagerfanta(
                        $paramFetcher->get('page'),
                        $paramFetcher->get('per_page'),
                        $paramFetcher->all()
                    )
                ),
                ['user']
            ),
            Response::HTTP_OK
        );
    }
}