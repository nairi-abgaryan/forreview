<?php

namespace App\EventListener;

use App\Entity\Image;
use App\Entity\User;
use App\HTTP\File\Base64EncodedFile;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\HTTP\File\UploadedBase64EncodedFile;

class ImageListener implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $args->getEntityManager();

        if ($entity instanceof Image) {
            $this->uploadImage($entity);
        }

        return;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $args->getEntityManager();

        if ($entity instanceof Image) {
            $cloudinary = $this->container->get("app.cloudinary_service");

            $cloudinary->remove($entity->getPath());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preRemove'];
    }

    /**
     * @param Image $image
     */
    public function uploadImage(Image $image)
    {
        $cloudinary = $this->container->get("app.cloudinary_service");
        /** @var User $owner */
        $owner = $this->container->get("security.token_storage")->getToken()->getUser();

        /** @var UploadedFile $file */
        $file = $image->getImage();
        $position = ($image->getPosition()) ? $image->getPosition() : $position = 0;
        $data = $this->is_url_exist($image->getPath());
        if ($data){
            $file = base64_encode($data);
            $file = new UploadedBase64EncodedFile(new Base64EncodedFile($file, true));
            $owner = $image->getOwner();
            $data = $cloudinary->upload($file);;
        }else{
            $data = $cloudinary->upload($file);
        }
        $image->setPath($data['public_id']);
        $image->setVersion($data['version']);
        $image->setFormat($data['format']);
        $image->setOwner($owner);
        $image->setPosition($position);
    }

    public function is_url_exist($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);
        if($result !== FALSE) {
            return $result;
        } else {
            return false;
        }
    }
}

