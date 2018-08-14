<?php

namespace App\Manager;

use App\Entity\Expert;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserLang;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use App\Service\MailerService;
use App\Service\StatusService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserManager
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MailerService
     */
    private $mailer;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * UserManager constructor.
     *
     * @param UserRepository $repository
     * @param EntityManagerInterface  $em
     * @param MailerService  $mailer
     * @param \Twig_Environment  $twig
     * @param UserPasswordEncoderInterface  $passwordEncoder
     * @param TokenGenerator  $tokenGenerator
     * @param string $baseUrl
     */
    public function __construct
    (
        UserRepository $repository,
        EntityManagerInterface $em,
        MailerService $mailer,
        \Twig_Environment $twig,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        $baseUrl
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param $id
     *
     * @return object|null|User
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param $query
     * @param $inAppStatus
     * @param $status
     * @param $role
     * @param $country
     * @param $city
     * @return QueryBuilder
     */
    public function search($query, $inAppStatus, $status, $role, $country, $city)
    {
        $qb = $this->repository->search($query, $inAppStatus, $status, $role, $country, $city);

        return $qb;
    }

    /**
     * @param array $criteria
     *
     * @return object|null|User
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAll()
    {
        $qb = $this->repository->createQueryBuilder('user');

        return $qb;
    }

    /**
     * @param $fbId
     * @param $email
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findWithEmailOrFacebookId($fbId, $email)
    {
        $qb = $this->repository->createQueryBuilder('user')
                ->where("user.email = :email")
                ->orWhere("user.facebookId = :fbId")
                ->setParameters(["fbId" => $fbId, "email" => $email])
                ->getQuery()
                ->getOneOrNullResult()
        ;

        return $qb;
    }

    /**
     * @return User
     */
    public function create()
    {
        return new User();
    }

    /**
     * @param array $socialAccount
     * @param $lang
     * @param Role $role
     * @return User|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createFromSocialAccount(array $socialAccount, $lang, Role $role)
    {
        $email = $socialAccount['email'] ?? $socialAccount['id'];
        $firstName = $socialAccount['first_name'] ?? null;
        $lastName = $socialAccount['last_name'] ?? null;
        $user = $this->findWithEmailOrFacebookId($socialAccount['id'], $email) ?? $this->create();
        $user->setEmail($email);
        $user->setFacebookId($email);
        $user->setPlainPassword($email);

        if ($role->getName() == "ROLE_EXPERT" && !$user->getExpert()){
            $expert = new Expert();
            $expert->setOwner($user);
            $expert->setStatus(StatusService::NOT_APPROVED);
            $user->setExpert($expert);
        }

        $user->setRole($role);
        $user->addRole($role);
        $user->setAppStatus(StatusService::PUBLISHED);

        $image = new Image();
        $image->setPath($socialAccount['picture']['data']['url']);
        $image->setPosition(0);
        $image->setOwner($user);
        $user->setAvatar($image);
        $user->setIsActive(true);
        $user->setTranslates([$lang]);
        $user->setSalt(md5(uniqid(rand(), true)));

        $userLang = new UserLang();
        $userLang->setFirstName($firstName);
        $userLang->setLastName($lastName);
        $userLang->setUser($user);
        $userLang->setLang('en');
        $user->setUserLang([$userLang]);
        $user = $this->persist($user);

        return $user;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function sendConfirmMail(User $user)
    {
        $token = $this->tokenGenerator->getTokenForActivation($user);
        /** @var  $template */
        $template = $this->twig->render("email/registration.confirmation.html.twig", [
            "token" => $token,
            "baseUrl" => $this->baseUrl
        ]);

        $subject = "Expago - Sign Up Confirm";
        $this->mailer->send($user, $template, $subject);
    }

    /**
     * @param  $email
     * @return bool
     * @throws \Exception
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function resetPassword($email)
    {
        $user = $this->findOneBy(["email" => $email]);

        if ($user){
            $token = $this->tokenGenerator->getTokenForActivation($user);
            $template = $this->twig->render("email/reset.password.html.twig", [
                "token" => $token,
                'user' => $user->getFirstName(),
                "baseUrl" => $this->baseUrl
            ]);

            $subject = "Expago - Reset Password";
            $this->mailer->send($user, $template, $subject);
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return User
     */
    public function persist(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials)
    {
        return $this->findOneBy(['email' => $credentials['email']]);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials)
    {
        $user = $this->getUser($credentials);
        if($user) {
            $password = $credentials['password'];
            if ($this->passwordEncoder->isPasswordValid($user, $password)) {
                return $user;
            }
            return false;
        };
        return false;
    }

    /**
     * @param User $user
     * @param $lang
     * @return User|null|object
     * @throws \Exception
     */
    public function createUser(User $user, $lang)
    {
        $NotActiveUser = $this->findOneBy(['email' => $user->getEmail(), "isActive" => false]);
        if ($NotActiveUser){
            $NotActiveUser->setPlainPassword($user->getPlainPassword());
            $user =  $NotActiveUser;
        }

        $user->setTranslates([$lang]);
        $user->addUserLang($user, $lang);
        $user->setSalt(md5(uniqid(rand(), true)));
        if ($user->getRole()->getName() == "ROLE_EXPERT"){
            $expert = new Expert();
            $expert->setStatus(StatusService::NOT_APPROVED);
            $expert->setOwner($user);
            $user->setExpert($expert);
        }

        $user = $this->persist($user);
        $this->sendConfirmMail($user);

        return $user;
    }

    /**
     * @param User $user
     * @param $lang
     * @return User|null|object
     * @throws \Exception
     */
    public function updateUser(User $user, $lang)
    {
        $NotActiveUser = $this->findOneBy(['email' => $user->getEmail(), "isActive" => false]);
        if ($NotActiveUser){
            $NotActiveUser->setPlainPassword($user->getPlainPassword());
            $user =  $NotActiveUser;
        }

        $user->setTranslates([$lang]);
        $user->addUserLang($user, $lang);
        $user->setSalt(md5(uniqid(rand(), true)));
        if ($user->getRole()->getName() == "ROLE_EXPERT"){
            $expert = new Expert();
            $expert->setStatus(StatusService::NOT_APPROVED);
            $expert->setOwner($user);
            $user->setExpert($expert);
        }

        $user = $this->persist($user);
        $this->sendConfirmMail($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function checkStatus(User $user)
    {
        if($user->isActive()) {
            return true;
        };
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function checkProfile(User $user)
    {
       $percent = 10;
       if ($user->getBio()){
           $percent += 15;
       }
       if ($user->getPreferences() && (boolean)count($user->getPreferences())){
           $percent += 10;
       }
       if ($user->getCity()){
           $percent += 10;
       }
       if ($user->getCountry()){
           $percent += 10;
       }
       if ($user->getDob()){
           $percent += 10;
       }
       if ($user->getSpokenLanguages() && (boolean)count($user->getSpokenLanguages())){
           $percent += 10;
       }
       if ($user->getPhone()){
           $percent += 15;
       }
       if ($user->getAvatar()){
           $percent += 15;
       }

       $user->setProfileCompletePercent($percent);
       return $user;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function remove(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();

        return true;
    }

    /**
     * @param User $user
     * @param  $password
     *
     * @return string
     */
    public function checkMatchPassword(User $user, $password)
    {
        return $this->passwordEncoder->isPasswordValid($user, $password);
    }
}
