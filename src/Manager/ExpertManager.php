<?php

namespace App\Manager;

use App\Entity\Expert;
use App\Entity\User;
use App\Repository\ExpertRepository;
use App\Resource\Constant;
use App\Service\HashService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExpertManager
{
    /**
     * @var ExpertRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var HashService
     */
    private $hashService;

    /**
     * @var Container
     */
    private $container;

    /**
     * UserManager constructor.
     *
     * @param ExpertRepository $repository
     * @param HashService $hashService
     * @param ContainerInterface $container
     * @param EntityManagerInterface  $em
     */
    public function __construct
    (
        ExpertRepository $repository,
        EntityManagerInterface $em,
        ContainerInterface $container,
        HashService $hashService
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->container = $container;
        $this->hashService = $hashService;
    }

    /**
     * @param $id
     *
     * @return object|null|Expert
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Expert
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
        $qb = $this->repository->createQueryBuilder('expert');

        return $qb;
    }

    /**
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function generateVerificationCode(User $user)
    {
        $code = rand(100000, 999999);
        $hashed = $this->hashService->hashValue($user, $code);
        $existing = $this->findOneBy(['verificationCode' => $hashed]);
        $expert = $user->getExpert();
        if (!$existing) {
            $expert->setVerificationCode($hashed);
            $expert->setPlainVerificationCode($code);
            $this->persist($expert);

            $this->container->get('app.sms_service')->sendVerificationCode($user);
            return $user;
        }

        $this->generateVerificationCode($user);
    }

    /**
     * @param Expert $expert
     * @return Expert
     */
    public function clearVerificationCode(Expert $expert)
    {
        $expert->setVerificationCode(null);

        return $this->changeStatus($expert,Constant::PENDING);
    }

    /**
     * @param Expert $expert
     * @param $status
     * @return Expert
     */
    public function changeStatus($expert, $status)
    {
        switch ($status) {
            case 0:
                $expert->setStatus(Constant::REJECTED);
                break;
            case 1:
                $expert->setStatus(Constant::ACTIVE);
                break;
            case 2:
                $expert->setStatus(Constant::PENDING);
                break;
            default:
                $expert->setStatus(Constant::NOT_APPROVED);
        }

        return $this->persist($expert);
    }

    /**
     * @return Expert
     */
    public function create()
    {
        return new Expert();
    }

    /**
     * @param Expert $expert
     * @return Expert
     */
    public function persist(Expert $expert)
    {
        $this->em->persist($expert);
        $this->em->flush();

        return $expert;
    }

    /**
     * @param User $user
     * @param $data
     * @return Expert
     */
    public function createExpert(User $user, $data)
    {
        $phone = $data['phone'];
        $expert = $this->create();
        $expert->setDocumentID($data['documentID']);
        $expert->setOwner($user);
        $user->setPhone($phone);
        $this->changeStatus($expert, Constant::PENDING);

        return $this->persist($expert);
    }

    /**
     * @param User $user
     * @param $data
     * @return bool
     */
    public function confirm(User $user, $data)
    {
        $verificationCode = $data['verificationCode'];
        if (!$this->hashService->checkUserVerificationCode($user, $verificationCode)) {
            return false;
        }

        $this->clearVerificationCode($user->getExpert());

        return true;
    }
}

