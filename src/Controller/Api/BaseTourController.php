<?php

namespace App\Controller\Api;

use App\Entity\BaseTour;
use App\Entity\Image;
use App\Exception\ValidationException;
use App\Form\Type\BaseTourType;
use App\Form\Type\ImageType;
use App\Form\Type\StatusActivateType;
use App\Manager\BaseTourManager;
use App\Manager\ImageManager;
use App\Service\PaginationFactory;
use App\Service\StatusService;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BaseTour
 * @Route("/base-tour")
 * @package App\Controller\Api
 */
class BaseTourController extends BaseController
{
    /**
     * @var baseTourManager
     */
    private $baseTourManager;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var StatusService
     */
    private $statusService;

    /**
     * BaseTour constructor.
     * @param baseTourManager $baseTourManager
     * @param ImageManager $imageManager
     * @param PaginationFactory $paginationFactory
     * @param StatusService $statusService
     */
    public function __construct
    (
        baseTourManager $baseTourManager,
        ImageManager $imageManager,
        PaginationFactory $paginationFactory,
        StatusService $statusService
    )
    {
        $this->baseTourManager = $baseTourManager;
        $this->imageManager = $imageManager;
        $this->paginationFactory = $paginationFactory;
        $this->statusService = $statusService;
    }

    /**
     * Get BaseTour  Translate
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_BaseTour")
     * @SWG\Response(
     *     response=200,
     *     description="BaseTour by id",
     * 	   @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=BaseTour::class, groups={"full"})
     *     )
     * )
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Base Tour")
     * @param BaseTour $baseTour
     * @return Response|View
     **/
    public function retrieve(BaseTour $baseTour)
    {
        return $this->response($baseTour, Response::HTTP_OK, "full");
    }

    /**
     * Get BaseTour List
     *
     * @Route("/", methods={"GET"}, name="app.get_base_tour_list")
     * @Security("has_role('ROLE_USER')")
     * @SWG\Response(
     *     response=200,
     *     description="Get BaseTour  List"
     * )
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         type="integer",
     *     ),
     * @SWG\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Page items count",
     *         type="integer",
     *  ),
     * @SWG\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query items",
     *         type="string",
     *  ),
     * @SWG\Parameter(
     *         name="in_app_status",
     *         in="query",
     *         description="In App Status value",
     *         type="integer",
     *  ),
     * @SWG\Parameter(
     *         name="activity",
     *         in="query",
     *         description="Filter by tour type",
     *         type="integer",
     *  ),
     *  @SWG\Parameter(
     *         name="city",
     *         in="query",
     *         description="Base Tour filter by city",
     *         type="string",
     *  ),
     * @SWG\Parameter(
     *         name="country",
     *         in="query",
     *         description="Base Tour filter by country",
     *         type="string",
     *  )
     * @SWG\Tag(name="Base Tour")
     * @param Request $request
     * @return Response|View
     **/
    public function list(Request $request)
    {
        $user  =  $this->getUser();
        $query = $request->query->get('query', null);
        $city = $request->query->get('city', null);
        $country = $request->query->get('country', null);
        $activity = $request->query->get('activity', null);
        $inAppStatus = $request->query->get('in_app_status', StatusService::PUBLISHED);
        $accessUser = $this->statusService->validateAppStatus($user, $inAppStatus);
        if (!$accessUser) {
            throw $this->createAccessDeniedException();
        }

        $data = $this->baseTourManager->search($query, $inAppStatus, $activity, $city, $country);
        $data = $this->paginationFactory->createCollection($data, $request,"app.get_vendors");
        return $this->response($data, Response::HTTP_OK, "full");
    }

    /**
     * Create BaseTour
     *
     * @Route("/", methods={"POST"}, name="app.create_base_tour")
     *
     * @SWG\Parameter(
     * 		name="BaseTour",
     * 		in="body",
     * 		required=true,
     *      @Model(type=BaseTourType::class)
     * ),
     * @SWG\Response(
     * 		response=201,
     * 		description="Base Tour creation success",
     * 		@SWG\Schema(
     *         type="json",
     *         ref=@Model(type=BaseTour::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Response(
     * 		response=400,
     * 		description="Validation failed",
     * 	)
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Base Tour")
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     **/
    public function create(Request $request)
    {
        $form = $this->createForm(BaseTourType::class, new BaseTour());
        $form->submit($request->request->all(), false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var BaseTour $data */
        $data = $form->getData();
        $this->baseTourManager->createBaseTour($data, $request->getLocale());
        return $this->response($data, Response::HTTP_CREATED, "full");
    }

    /**
     * Update BaseTour
     *
     * @Route("/{id}/", methods={"PUT"}, name="app.update_base_tour")
     *
     * @SWG\Parameter(
     * 		name="BaseTour",
     * 		in="body",
     * 		required=true,
     *      @Model(type=BaseTourType::class, groups={"full"})
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="BaseTour by id",
     * 	   @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=BaseTour::class, groups={"full"})
     *     )
     * ),
     * @SWG\Response(
     * 		response=400,
     * 		description="Validation failed",
     * 	)
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Base Tour")
     * @param Request $request
     * @param BaseTour $baseTour
     *
     * @return mixed
     * @throws ValidationException
     **/
    public function update(Request $request, BaseTour $baseTour)
    {
        $form = $this->createForm(BaseTourType::class, $baseTour);
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var BaseTour $data */
        $data = $form->getData();
        $baseTour = $this->baseTourManager->updateBaseTour($data, $request->getLocale());

        return $this->response($baseTour, Response::HTTP_OK, "full");
    }

    /**
     * Add BaseTour images
     *
     * @Route("/{id}/images", methods={"PATCH"}, name="app.add_base_tour_images")
     *
     * @SWG\Parameter(
     * 		name="baseTour",
     * 		in="body",
     * 		required=true,
     *      @Model(type=ImageType::class, groups={"full"})
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="BaseTour by id",
     * 	   @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=BaseTour::class, groups={"full"})
     *     )
     * ),
     * @SWG\Response(
     * 		response=400,
     * 		description="Validation failed",
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Base Tour")
     * @param Request $request
     * @param BaseTour $baseTour
     *
     * @return mixed
     * @throws ValidationException
     **/
    public function addImages(Request $request, BaseTour $baseTour)
    {
        $form = $this->createForm(ImageType::class, new Image());
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Image $data */
        $data = $form->getData();
        $image = $this->imageManager->persist($data);
        $baseTour->addImages($image);
        $baseTour = $this->baseTourManager->persist($baseTour);

        return $this->response($baseTour, Response::HTTP_OK, "full");
    }

    /**
     * Delete BaseTour
     *
     * @Route("/{id}/", methods={"DELETE"}, name="app.delete_base_tour")
     *
     * @SWG\Response(
     * 		response=204,
     * 		description="Delete BaseTour"
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Base Tour")
     * @param BaseTour $baseTour
     *
     * @return mixed
     **/
    public function delete(BaseTour $baseTour)
    {
        $this->baseTourManager->remove($baseTour);

        return $this->response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Publish base tour.
     *
     * @Route("/{id}/publish", methods={"PUT"}, name="app.publish_base_tour")
     * @Security("has_role('ROLE_ADMIN')")
     * @SWG\Parameter(
     *     name="status",
     *     in="body",
     *     required=false,
     *     @Model(type=StatusActivateType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="BaseTour by id",
     * 	   @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=BaseTour::class, groups={"full"})
     *     )
     * ),
     * @SWG\Response(
     * 		response=400,
     * 		description="Validation failed",
     * 	),
     * @SWG\Tag(name="BaseTour")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param BaseTour $baseTour
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function publish(BaseTour $baseTour, Request $request)
    {
        $form = $this->createForm(StatusActivateType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        $data = $form->getData();
        /** @var BaseTour $baseTour */
        $baseTour = $this->statusService->changeAppStatus($baseTour, $data['status']);
        $baseTour = $this->baseTourManager->persist($baseTour);
        return $this->response($baseTour, Response::HTTP_OK, "full");
    }
}

