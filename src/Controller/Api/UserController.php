<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\ValidationException;
use App\Form\Model\ChangePassword;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\StatusActivateType;
use App\Manager\CityManager;
use App\Manager\ImageManager;
use App\Manager\UserManager;
use App\Form\Type\UserType;
use App\Security\TokenGenerator;
use App\Service\PaginationFactory;
use App\Service\StatusService;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 *
 * @Route("/users")
 * @package App\Controller\Api
 */
class UserController extends BaseController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var CityManager
     */
    private $cityManager;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var StatusService
     */
    private $statusService;

    /**
     * UserController constructor.
     * @param UserManager $userManager
     * @param CityManager $cityManager
     * @param ImageManager $imageManager
     * @param TokenGenerator $tokenGenerator
     * @param PaginationFactory $paginationFactory
     * @param ViewHandlerInterface $viewHandler
     * @param StatusService $statusService
     */
    public function __construct
    (
        UserManager $userManager,
        CityManager $cityManager,
        ImageManager $imageManager,
        TokenGenerator $tokenGenerator,
        PaginationFactory $paginationFactory,
        ViewHandlerInterface $viewHandler,
        StatusService $statusService
    )
    {
        $this->userManager = $userManager;
        $this->cityManager = $cityManager;
        $this->imageManager = $imageManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->paginationFactory = $paginationFactory;
        $this->viewHandler = $viewHandler;
        $this->statusService = $statusService;
    }

    /**
     * Get User
     *
     * @Route("/me/", methods={"GET"}, name="app.get_user")
     * @SWG\Response(
     *     response=200,
     *     description="Get User"
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="User")
     * @return Response|View
     **/
    public function retrieve()
    {
        $user = $this->getUser();
        $view = $this->view($user, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Get User with id
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_user_id")
     * @SWG\Response(
     *     response=200,
     *     description="Get User"
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="User")
     * @return Response|View
     **/
    public function getUserInfo(User $user)
    {
        $view = $this->view($user, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Get Users List
     * @Route("/", methods={"GET"}, name="app.get_users_list")
     * @SWG\Response(
     *     response=200,
     *     description="Get Users  List"
     * ),
     * @SWG\Parameter(
     *     name="role",
     *     in="query",
     *     type="string",
     *     required=false,
     *     description="Role parameter",
     * ),
     * @SWG\Parameter(
     *     name="query",
     *     in="query",
     *     type="string",
     *     required=false,
     *     description="Search query parameters",
     * )
     * @SWG\Parameter(
     *     name="in_app_status",
     *     in="query",
     *     description="In App Status value",
     *     type="integer",
     *  ),
     * @SWG\Parameter(
     *     name="status",
     *     in="query",
     *     description="Status for experts",
     *     type="integer"
     * )
     * @SWG\Parameter(
     *     name="country",
     *     in="query",
     *     description="Country query parameter",
     *     type="string"
     * ),
     * @SWG\Parameter(
     *     name="city",
     *     in="query",
     *     description="City query parameter",
     *     type="string"
     * )
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="User")
     * @param Request $request
     * @return Response|View
     **/
    public function list(Request $request)
    {
        $user = $this->getUser();
        $query = $request->query->get('query', null);
        $inAppStatus = $request->query->get('in_app_status', StatusService::PUBLISHED);
        $status = $request->query->get('status', null);
        $role = $request->query->get('role', null);
        $country = $request->query->get('country', null);
        $city = $request->query->get('city', null);
        $accessUser = $this->statusService->validateAppStatus($user, $inAppStatus);
        if (!$accessUser) {
            throw $this->createAccessDeniedException();
        }

        $data = $this->userManager->search($query, $inAppStatus, $status, $role, $country, $city);
        $data = $this->paginationFactory->createCollection($data, $request,"app.get_users_list");
        $view = $this->view($data, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Create user.
     *
     * @Route("/", methods={"POST"}, name="app.create_user")
     *
     * @SWG\Parameter(
     * 		name="User",
     * 		in="body",
     * 		required=true,
     *      @Model(type=UserType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="User token"
     * ),
     * @SWG\Tag(name="User")
     * @param Request $request
     * @SWG\Parameter(ref="#parameters/languages"),
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $form = $this->createForm(UserType::class);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var User $data */
        $data = $form->getData();
        $this->userManager->createUser($data, $request->getLocale());
        $view = $this->view([], 201);

        return $this->viewHandler->handle($view);
    }


    /**
     * Update User
     *
     * @Route("/{id}/", methods={"PUT"}, name="app.update_user")
     * @Security("has_role('ROLE_USER','ROLE_ADMIN','ROLE_EXPERT')")
     * @SWG\Parameter(
     * 		name="user",
     * 		in="body",
     * 		required=true,
     *      @Model(type=UserType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Update User",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=UserType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="User")
     * @param Request $request
     *
     * @param User $user
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, User $user)
    {
        if ($user !== $this->getUser() && !in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid() ) {
            throw new ValidationException($form);
        }

        /** @var User $data */
        $data = $form->getData();
        $lang = $data->getTranslates();
        if(!in_array($request->getLocale(), $data->getTranslates())){
            array_push($lang, $request->getLocale());
            $data->setTranslates($lang);
        }

        if (!is_null($data->getAvatar()) && $data->getAvatar()->getImage()){
            $this->imageManager->update($data->getAvatar(), $this->getUser());
        }

        $data = $this->cityManager->addCity($data);
        $data->addUserLang($data, $request->getLocale());

        /** @var User $user */
        $data  = $this->userManager->checkProfile($user);
        $user = $this->userManager->persist($data);
        $view = $this->view($user, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * User activation
     *
     * @Route("/activate", methods={"PATCH"}, name="app.user_activate")
     * @Security("has_role('ROLE_USER')")
     * @SWG\Response(
     * 		response=200,
     * 		description="Update User",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=UserType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="User")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function activate(Request $request)
    {
        $token = $request->headers->get("Authorization");
        $tokenData = $this->tokenGenerator->decode($token);
        /** @var User $user */
        $user = $this->getUser();
        if (!$user || !isset($tokenData['activate'])) {
            throw $this->createAccessDeniedException();
        }
        $user->setIsActive(true);
        $this->userManager->persist($user);
        $view = $this->view([], 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * User activation
     *
     * @Route("/reset-password", methods={"POST"}, name="app.reset_password")
     * @SWG\Parameter(
     *     name="email",
     *     in="body",
     *     @SWG\Schema(
     *         type="string",
     *         description="Reset password email",
     *         @SWG\Property(type="string", title="email", property="email")
     *      )
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Update User"
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="User")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function resetPassword(Request $request)
    {
        $email = $request->request->get('email');
        $resetPassword = $this->userManager->resetPassword($email);
        $view = $this->view(null, 200);

        if (!$resetPassword){
            $view = $this->view(["message" => "No such account"], 400);
        }

        return $this->viewHandler->handle($view);
    }

    /**
     * Publish user.
     *
     * @Route("/{id}/publish", methods={"PUT"}, name="app.publish_user")
     * @Security("has_role('ROLE_ADMIN')")
     * @SWG\Parameter(
     *     name="status",
     *     in="body",
     *     required=false,
     *     @Model(type=StatusActivateType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="User publish for admin"
     * ),
     * @SWG\Tag(name="User")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param User $user
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function publish(User $user, Request $request)
    {
        $form = $this->createForm(StatusActivateType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }
        $data = $form->getData();

        /** @var User $user */
        $user = $this->statusService->changeAppStatus($user, $data['status']);
        $this->userManager->persist($user);
        $view = $this->view($user, 200);

        return $this->handleView($view);
    }

    /**
     * Delete User
     * @Security("has_role('ROLE_USER')")
     * @Route("/{id}/", methods={"DELETE"}, name="app.delete_user")
     *
     * @SWG\Response(
     * 		response=204,
     * 		description="Delete vendor"
     * 	),
     *
     * @SWG\Tag(name="User")
     * @param User $user
     *
     * @return Response
     */
    public function delete(User $user)
    {
        $this->userManager->remove($user);

        $view = $this->view([], 204);

        return $this->handleView($view);
    }

    /**
     * Change User password
     * @Security("has_role('ROLE_USER')")
     * @Route("/change-password", methods={"PUT"}, name="app.change_password")
     * @SWG\Parameter(
     *     name="changePassword",
     *     in="body",
     *     required=false,
     *     @Model(type=ChangePasswordType::class, groups={"list"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="User password change"
     * 	),
     *
     * @SWG\Tag(name="User")
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function changePassword(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var ChangePassword $data */
        $data = $form->getData();
        $matchOldPassAndCurrent = $this->userManager->checkMatchPassword($this->getUser(), $data->getPlainPassword());

        if ($matchOldPassAndCurrent){
            $view = $this->view(["message" => "new password must not have been used in the past"], 400);

            return $this->handleView($view);
        }

        $user->setPlainPassword($data->getPlainPassword());
        $user = $this->userManager->persist($user);

        $view = $this->view($user, 200);

        return $this->handleView($view);
    }
}

