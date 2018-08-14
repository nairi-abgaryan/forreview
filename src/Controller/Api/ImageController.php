<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Exception\ValidationException;
use App\Form\Type\ImageType;
use App\Manager\ImageManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImageController
 * @package App\Controller
 * @Route("/image")
 */
class ImageController extends FOSRestController
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * ImageController constructor.
     * @param ImageManager $imageManager
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(ImageManager $imageManager, ViewHandlerInterface  $viewHandler)
    {
        $this->imageManager = $imageManager;
        $this->viewHandler = $viewHandler;
    }

    /**
     * Upload image.
     *
     * @Route("/", methods={"POST"}, name="api.create_image")
     * @SWG\Parameter(
     * 		name="Image",
     * 		in="body",
     * 		required=true,
     *      @Model(type=ImageType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="success",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=ImageType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Tag(name="Images")
     * @param Request $request
     * @return View|Response
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $form = $this->createForm(ImageType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Image $data */
        $data = $form->getData();
        $image = $this->imageManager->persist($data);
        $view = $this->view($image, 201);

        return $this->viewHandler->handle($view);
    }

    /**
     * Delete Images
     * @SWG\Response(
     * 		response=204,
     * 		description="Delete Image"
     * 	),
     * @Route("/{id}/", methods={"DELETE"}, name="app.delete_image")
     * @SWG\Tag(name="Images")
     *
     * @param Image $image
     * @return mixed
     **/
    public function delete(Image $image)
    {
        $this->imageManager->remove($image);

        $view = $this->view([], 204);
        return $this->viewHandler->handle($view);
    }
}

