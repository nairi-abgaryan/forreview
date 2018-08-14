<?php

namespace App\Service;

use App\Entity\Image;
use Cloudinary\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryService
{
    /**
     * @var string
     */
    private $cloudName;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var string
     */
    private $cloudinaryFolder;

    /**
     * @var \Cloudinary
     */
    private $cloudinary;

    /**
     * ImageListener constructor.
     *
     * @param string $cloudinary_cloud_name
     * @param string $cloudinary_api_key
     * @param string $cloudinary_api_secret
     * @param string $cloudinary_folder
     */
    public function __construct($cloudinary_cloud_name, $cloudinary_api_key, $cloudinary_api_secret, $cloudinary_folder)
    {
        $this->cloudName = $cloudinary_cloud_name;
        $this->apiKey = $cloudinary_api_key;
        $this->apiSecret = $cloudinary_api_secret;
        $this->cloudinaryFolder = $cloudinary_folder;

        $this->cloudinary = \Cloudinary::config([
            'cloud_name' => $this->cloudName,
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
        ]);
    }

    /**
     * @param UploadedFile $file
     *
     * @return array
     */
    public function upload(UploadedFile $file)
    {
        return Uploader::upload($file, [
            'public_id' => $this->cloudinaryFolder . '/' . md5(uniqid())
        ]);
    }

    /**
     * @param string $publicId
     *
     * @return mixed
     */
    public function remove($publicId)
    {
        return Uploader::destroy($publicId);
    }

    /**
     * @param Image $image
     *
     * @return string
     */
    public function getImageUrl(Image $image)
    {
        return cloudinary_url($image->getPath(), [
            'version' => $image->getVersion(),
            'format' => $image->getFormat(),
        ]);
    }
}
