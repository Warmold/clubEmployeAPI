<?php


namespace App\Controller;

use App\Assembler\Invitation\InvitationAssembler;
use App\Dto\Invitation\InvitationDto;
use App\Entity\Invitation\Invitation;
use App\Manager\Invitation\InvitationManager;
use App\Serializer\JsonSerializer;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class InvitationController
 */
class InvitationController
{
    public InvitationManager   $invitationManager;

    public InvitationAssembler $invitationAssembler;

    public JsonSerializer      $serializer;

    public ValidatorInterface   $validator;

    /**
     * InvitationController constructor.
     *
     * @param InvitationManager     $invitationManager
     * @param InvitationAssembler   $invitationAssembler
     * @param JsonSerializer        $serializer
     */
    public function __construct(
        InvitationManager $invitationManager,
        InvitationAssembler $invitationAssembler,
        JsonSerializer $serializer
    ){
        $this->invitationManager    = $invitationManager;
        $this->invitationAssembler  = $invitationAssembler;
        $this->serializer           = $serializer;
    }

    /**
     * @Route("invitations", name="get_invitations", methods={"GET"})
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1")
     * @Rest\QueryParam(name="per_page", requirements="5|10|20|50", default="10")
     * @Rest\QueryParam(name="order_by", requirements="id|code|name|enabled")
     * @Rest\QueryParam(name="order_direction", requirements="(asc|desc)", default="asc")
     * @Rest\QueryParam(name="filters", map=true)
     * @Rest\QueryParam(name="search", requirements="\w+")
     *
     * @param ParamFetcherInterface   $paramFetcher
     *
     * @return Response
     *
     * @throws \ReflectionException
     */
    public function getInviations(Security $security, ParamFetcherInterface $paramFetcher): Response
    {
        return new Response(
            $this->serializer->serialize(
                $this->invitationAssembler->transformPagerfanta(
                    $this->invitationManager->getPagerfanta(
                        $paramFetcher->get('page'),
                        $paramFetcher->get('per_page'),
                        $paramFetcher->all()
                    )
                ),
                ['invitation']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("invitations/{uuid}", name="get_invitation", methods={"GET"})
     *
     * @param string $uuid
     *
     * @return Response
     */
    public function getInvitation(string $uuid): Response
    {
        $invitation = $this->invitationManager->findOneByUuid($uuid);

        if (null === $invitation) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Invitation not found');
        }

        return new Response(
            $this->serializer->serialize(
                $this->invitationAssembler->transform(
                    $invitation
                ),
                ['invitation']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("invitations", name="post_invitation", methods={"POST"})
     *
     * @ParamConverter("invitationDto", converter="app.request_params")
     *
     * @param InvitationDto                    $invitationDto
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function postInvitation(
        InvitationDto                    $invitationDto,
        Security                         $security,
        ConstraintViolationListInterface $validationErrors
    ): Response {

        if (count($validationErrors) > 0) {
            return new Response(
                $this->serializer->serialize(
                    $validationErrors
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $invitation = $this->invitationAssembler->reverseTransform($invitationDto);
        $invitation->setSender($security->getUser());
        $this->invitationManager->save($invitation);

        return new Response(
            $this->serializer->serialize(
                $this->invitationAssembler->transform(
                    $invitation
                ),
                ['invitation']
            ),
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("invitations/{uuid}", name="put_invitation", methods={"PUT"})
     *
     * @param string         $uuid
     * @param Request        $request
     *
     * @return Response
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Exception
     *
     * @return Response
     */
    public function putInvitation(string $uuid, Request $request): Response
    {
        $invitation = $this->invitationManager->findOneByUuid($uuid);

        if (null === $invitation) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Invitation not found');
        }

        $invitationDto = $this->invitationAssembler->transformAndPatch($invitation, \json_decode($request->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR));

        $invitation = $this->invitationAssembler->reverseTransform($invitationDto, $invitation);
        $this->invitationManager->save($invitation);

        return new Response(
            $this->serializer->serialize(
                $this->invitationAssembler->transform(
                    $invitation
                ),
                ['invitation']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("invitations/{uuid}/confirm", name="post_invitation_confirm", methods={"POST"})
     *
     * @param string $uuid
     * @return Response
     *
     * @throws \Exception
     */
    public function confirmInvitation(
        string $uuid
    ): Response {
        if (!$invitation = $this->invitationManager->findOneByUuid($uuid)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Invitation not found');
        }

        $invitation->setState(Invitation::STATE_ACCEPTED);

        $this->invitationManager->save($invitation);

        return new Response(
            $this->serializer->serialize(
                $this->invitationAssembler->transform(
                    $invitation
                ),
                ['invitation']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("invitations/{uuid}/refuse", name="post_invitation_refuse", methods={"POST"})
     *
     * @param string $uuid
     * @return Response
     *
     * @throws \Exception
     */
    public function refuseInvitation(
        string $uuid
    ): Response {
        if (!$invitation = $this->invitationManager->findOneByUuid($uuid)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Invitation not found');
        }

        $invitation->setState(Invitation::STATE_REFUSED);

        $this->invitationManager->save($invitation);

        return new Response(
            $this->serializer->serialize(
                $this->invitationAssembler->transform(
                    $invitation
                ),
                ['invitation']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("invitations/{uuid}/cancel", name="post_invitation_cancel", methods={"POST"})
     *
     * @param string $uuid
     * @return Response
     *
     * @throws \Exception
     */
    public function cancelInvitation(
        string $uuid
    ): Response {
        if (!$invitation = $this->invitationManager->findOneByUuid($uuid)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Invitation not found');
        }

        $invitation->setState(Invitation::STATE_CANCELED);

        $this->invitationManager->save($invitation);

        return new Response(
            $this->serializer->serialize(
                $this->invitationAssembler->transform(
                    $invitation
                ),
                ['invitation']
            ),
            Response::HTTP_OK
        );
    }
}