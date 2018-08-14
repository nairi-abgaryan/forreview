<?php

namespace App\Manager;

use App\Entity\Image;
use App\Entity\User;
use App\Repository\ImageRepository;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageManager
{
    /**
     * @var ImageRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CloudinaryService
     */
    private $cloudinary;

    /**
     * CountryManager constructor.
     *
     * @param ImageRepository $repository
     * @param CloudinaryService $cloudinary
     * @param EntityManagerInterface  $em
     */
    public function __construct
    (
        ImageRepository $repository,
        EntityManagerInterface $em,
        CloudinaryService $cloudinary
    )
    {
        $this->repository = $repository;
        $this->cloudinary = $cloudinary;
        $this->em = $em;
    }


    /**
     * @param $id
     *
     * @return object|null|Image
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Image
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
    }

    /**
     * @return Image
     */
    public function create()
    {
        return new Image();
    }

    /**
     * In persist running event prePersist ImageListener for images
     * @param Image $image
     * @return Image
     */
    public function persist(Image $image)
    {
        $this->em->persist($image);
        $this->em->flush();
        return $image;
    }

    /**
     * In persist running event preRemove ImageListener for images
     * @param Image $image
     * @return mixed
     */
    public function remove(Image $image)
    {
        $this->em->remove($image);
        $this->em->flush();

        return true;
    }

    /**
     * @param Image $image
     * @param User $owner
     * @return Image
     */
    public function update(Image $image, User $owner)
    {
        if ($image->getPath()){
            $this->cloudinary->remove($image->getPath());
        }

        /** @var UploadedFile $file */
        $file = $image->getImage();
        $position = ($image->getPosition()) ? $image->getPosition() : $position = 0;
        $data = $this->cloudinary->upload($file);
        $image->setPath($data['public_id']);
        $image->setVersion($data['version']);
        $image->setFormat($data['format']);
        $image->setOwner($owner);
        $image->setPosition($position);

        return $image;
    }
}
