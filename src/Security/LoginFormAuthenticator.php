<?php

namespace App\Security;

use App\Form\Type\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $pathSegments = [];

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface        $em
     * @param RouterInterface      $router
     * @param UserPasswordEncoderInterface  $passwordEncoder
     * @param RequestStack         $requestStack
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        RequestStack $requestStack
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->requestStack = $requestStack;

        $request = $this->requestStack->getCurrentRequest();

        if ($request) {
            $this->pathSegments = explode('/', $request->getPathInfo());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() === '/admin/login/' && $request->isMethod('POST');
        $form = $this->formFactory->create(LoginType::class);
        if (!$isLoginSubmit) {
            return null;
        }

        $form->handleRequest($request);
        $data = $form->getData();
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['email']
        );

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        return $this->em->getRepository('App:User')
            ->findOneBy(['email' => $credentials['email']]);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $adminPage =  $this->router->generate('sonata_admin_dashboard');

        return new RedirectResponse($adminPage);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSuccessRedirectURL()
    {
        return $this->router->generate('sonata_admin_dashboard');
    }


    public function supportsRememberMe()
    {
        return true;
    }

    public function supports(Request $request)
    {
        if(isset($request->request->all()['login'])){
            return true;
        }
        return false;
    }

    /**
     * Override to control what happens when the user hits a secure page
     * but isn't logged in yet.
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->getLoginUrl();

        return new RedirectResponse($url);
    }
}