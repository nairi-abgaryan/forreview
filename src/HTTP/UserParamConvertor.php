<?php

namespace App\HTTP;

use App\Entity\User;
use App\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserParamConvertor implements ParamConverterInterface
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserParamConverter constructor.
     *
     * @param UserManager  $userManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(UserManager $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = $request->attributes->get('id');

        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        if (!$id || $id === '') {
            throw new NotFoundHttpException();
        }

        if ($id !== 'me') {
            $user = $this->userManager->findOneBy(['id' => $id]);
        } else {
            /** @var User $user */
            $user = $token->getUser();
        }

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $request->attributes->set($configuration->getName(), $user);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return 'App\Entity\User' === $configuration->getClass();
    }
}
