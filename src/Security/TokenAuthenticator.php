<?php

namespace App\Security;

use App\Manager\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param JWTEncoderInterface $encoder
     * @param UserManager         $userManager
     */
    public function __construct(JWTEncoderInterface $encoder, UserManager $userManager)
    {
        $this->encoder = $encoder;
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return array(
            'token' => $request->headers->get('Authorization'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $data = $this->encoder->decode($credentials['token']);

        if ($data === false || !isset($data['id'])) {
            throw new CustomUserMessageAuthenticationException('Invalid token!');
        }

        $user =  $this->userManager->findOneBy(['id' => $data['id']]);
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['error' => $exception->getMessageKey()], 401);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;

    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $message = $authException ? $authException->getMessageKey() : 'Missing credentials';

        return new JsonResponse(['error' => $message], 401);
    }
}
