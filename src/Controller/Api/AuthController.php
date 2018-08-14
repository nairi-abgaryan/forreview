<?php

namespace App\Controller\Api;

use App\Entity\Role;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Form\Type\FacebookLoginType;
use App\Form\Type\LoginType;
use App\Form\Type\RoleType;
use App\Manager\RoleManager;
use App\Manager\UserManager;
use App\Security\LoginFormAuthenticator;
use App\Security\TokenGenerator;
use App\Service\FacebookService;
use Doctrine\ORM\Mapping as ORM;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 * @package App\Controller\Api
 */
class AuthController extends FOSRestController
{
    /**
     * @var   ViewHandlerInterface $viewHandler
     */
    private $viewHandler;

    /**
     * @var FacebookService
     * @ORM\Column(type="string")
     */
    private $facebookService;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var LoginFormAuthenticator
     */
    private $loginFormAuthenticator;
    /**
     * AuthController constructor.
     *
     * @param FacebookService $facebookService
     * @param UserManager     $userManager
     * @param RoleManager     $roleManager
     * @param TokenGenerator  $tokenGenerator
     * @param ViewHandlerInterface  $viewHandler
     * @param LoginFormAuthenticator  $loginFormAuthenticator
     */
    public function __construct(
        FacebookService $facebookService,
        UserManager $userManager,
        RoleManager $roleManager,
        TokenGenerator $tokenGenerator,
        ViewHandlerInterface $viewHandler,
        LoginFormAuthenticator $loginFormAuthenticator
    ) {
        $this->facebookService = $facebookService;
        $this->userManager = $userManager;
        $this->roleManager = $roleManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->viewHandler = $viewHandler;
        $this->loginFormAuthenticator = $loginFormAuthenticator;
    }

    /**
     * Login with Facebook.
     *
     * @Route("/login/facebook/", name="api.auth_login_with_facebook")
     * @Method({"POST"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="User auth with facebook",
     *      @SWG\Schema(
     *         @Model(type=FacebookLoginType::class, groups={"non_sensitive_data"})
     *     )
     * ),
     *
     * @SWG\Parameter(
     * 			name="user",
     * 			in="body",
     * 			required=true,
     *          @Model(type=FacebookLoginType::class, groups={"non_sensitive_data"})
     *	)
     *
     * @SWG\Tag(name="Auth")
     * @param Request $request
     * @SWG\Parameter(ref="#parameters/languages"),
     * @return Response|View
     * @throws ValidationException
     */
    public function loginWithFacebookAction(Request $request)
    {
        $form = $this->createForm(FacebookLoginType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        $data = $form->getData();
        $accessToken = new AccessToken(['access_token' => $data['access_token']]);
        /** @var Role $role */
        $role = $data['role'];

        try {
            $facebookUser = $this->facebookService->getUserData($accessToken->getToken());
            $role = $role ? $role : $this->roleManager->findOneBy(["name" => "ROLE_USER"]);
            $user = $this->userManager->createFromSocialAccount($facebookUser, $request->getLocale(), $role);
            $token = $this->tokenGenerator->getTokenForUser($user);
            $view = $this->view(['access_token' => $token], Response::HTTP_OK);

            /** check user roles count and return 202 status code for switching account */
            if(count($user->getRoles()) > 1) {
                $view = $this->view(['access_token' => $token], Response::HTTP_ACCEPTED);
                return $this->viewHandler->handle($view);
            }
            return $this->viewHandler->handle($view);
        } catch (\Exception $e) {
            $view = $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            return $this->viewHandler->handle($view);
        }
    }

    /**
     *  Login/register with Email password.
     *
     * @Route("/login/", name="api.login")
     * @Method({"POST"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="User auth with email",
     *      @SWG\Schema(
     *         @Model(type=LoginType::class, groups={"non_sensitive_data"})
     *     )
     * ),
     * @SWG\Parameter(
     * 			name="user",
     * 			in="body",
     * 			required=true,
     *          @Model(type=LoginType::class, groups={"non_sensitive_data"})
     *	)
     * @SWG\Tag(name="Auth")
     * @param Request $request
     *
     * @return Response|View|static
     *
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     * @throws ValidationException
     */
    public function loginWithCredentials(Request $request)
    {
        $form = $this->createForm(LoginType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }
        $data = $form->getData();

        $user = $this->userManager->checkCredentials($data);
        if (!$user) {
            return $this->view(["message" => "Incorrect login"], Response::HTTP_UNAUTHORIZED);
        }

        $status = $this->userManager->checkStatus($user);
        if(!$status){
            $view = $this->view(['message' => 'Your account is not activated'], Response::HTTP_FORBIDDEN);
            return $this->viewHandler->handle($view);
        }

        $token = $this->tokenGenerator->getTokenForUser($user);
        /** check user roles count and return 202 status code for switching account */
        if(count($user->getRoles()) > 1) {
            $view = $this->view(['access_token' => $token], Response::HTTP_ACCEPTED);
            return $this->viewHandler->handle($view);
        }
        $view = $this->view(['access_token' => $token], Response::HTTP_OK);

        return $this->viewHandler->handle($view);
    }

    /**
     * Switch user account
     *
     * @Route("/switch/", name="api.switch_account")
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')")
     * @SWG\Response(
     *     response=200,
     *     description="User access token"
     * ),
     * @SWG\Parameter(
     *     type="string",
     *     name="role",
     *     in="body",
     *     @Model(type=RoleType::class)
     * ),
     * @SWG\Tag(name="Auth")
     *
     * @param Request $request
     * @return Response|View|static
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     * @throws ValidationException
     */
    public function switchAccount(Request $request)
    {
        $form = $this->createForm(RoleType::class);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Role $data */
        $role = $form->getData()["role"];
        /** @var User $user */
        $user = $this->getUser();
        $user->setRole($role);
        $this->userManager->persist($user);
        $token = $this->tokenGenerator->getTokenForUser($user);
        $view = $this->view(['access_token' => $token], Response::HTTP_OK);
        return $this->viewHandler->handle($view);
    }
}
