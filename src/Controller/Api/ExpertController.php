<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\ValidationException;
use App\Form\Type\StatusActivateType;
use App\Form\Type\ExpertType;
use App\Form\Type\VerifyType;
use App\Manager\ExpertManager;
use App\Manager\UserManager;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Expert;
/**
 * Class RoleController
 *
 * @Route("/expert")
 * @package App\Controller\Api
 */
class ExpertController extends BaseController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var ExpertManager
     */
    private $expertManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * VendorController constructor.
     * @param UserManager $userManager
     * @param ExpertManager $expertManager
     * @param ValidatorInterface $validator
     */
    public function __construct
    (
        UserManager $userManager,
        ExpertManager $expertManager,
        ValidatorInterface $validator
    )
    {
        $this->userManager = $userManager;
        $this->expertManager = $expertManager;
        $this->validator = $validator;
    }

    /**
     * Create expert.
     *
     * @Route("/", methods={"POST"}, name="app.create_expert")
     * @Security("has_role('ROLE_EXPERT')")
     * @SWG\Parameter(
     * 		name="Expert",
     * 		in="body",
     * 		required=true,
     *      @Model(type=ExpertType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Expert",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Expert::class)
     *     )
     * ),
     * @SWG\Tag(name="Expert"),
     * @SWG\Parameter(ref="#parameters/languages")
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $form = $this->createForm(ExpertType::class);
        $form->submit($request->request->all());

        if(!$form->isValid()){
            throw new ValidationException($form);
        };

        $data = $form->getData();
        $expert = $this->expertManager->createExpert($this->getUser(), $data);

        return $this->response($expert, Response::HTTP_OK);
    }

    /**
     * Create verification code .
     *
     * @Route("/verification", methods={"POST"}, name="app.verification_expert")
     * @Security("has_role('ROLE_EXPERT')")
     * @SWG\Response(
     *     response=201,
     *     description="Expert"
     * ),
     * @SWG\Tag(name="Expert")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function sendVerificationCode()
    {
        $user = $this->getUser();
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->response($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->expertManager->generateVerificationCode($user);

        return $this->response($user, Response::HTTP_OK);
    }

    /**
     * Confirm expert.
     *
     * @Route("/confirm", methods={"POST"}, name="app.confirm_expert")
     * @Security("has_role('ROLE_EXPERT')")
     * @SWG\Parameter(
     * 		name="Expert",
     * 		in="body",
     * 		required=true,
     *      @Model(type=VerifyType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Expert"
     * ),
     * @SWG\Tag(name="Expert")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws ValidationException
     */
    public function confirm(Request $request)
    {
        $form = $this->createForm(VerifyType::class);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        $data = $form->getData();
        $confirm = $this->expertManager->confirm($this->getUser(), $data);
        if (!$confirm){
            return $this->response(['error' => 'Wrong verification code.'], Response::HTTP_BAD_REQUEST);
        }

        return $this->response($this->getUser(), Response::HTTP_OK);
    }

    /**
     * Activate expert.
     *
     * @Route("/{id}/activate", methods={"PUT"}, name="app.activate_expert")
     * @Security("has_role('ROLE_ADMIN')")
     * @SWG\Parameter(
     *     name="status",
     *     in="body",
     *     required=false,
     *     @Model(type=StatusActivateType::class)
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Expert confirmation for admin",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=User::class, groups={"full"})
     *     )
     * ),
     * @SWG\Tag(name="Expert")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param User $user
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function activate(User $user, Request $request)
    {
        $form = $this->createForm(StatusActivateType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var User $user */
        $expert = $user->getExpert();
        $data = $form->getData();
        $this->expertManager->changeStatus($expert, $data['status']);
        $view = $this->view($expert->getOwner(), Response::HTTP_OK);

        return $this->handleView($view);
    }
}

