<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Entity\Tour;
use App\Exception\ValidationException;
use App\Form\Type\StatusActivateType;
use App\Form\Type\ImageType;
use App\Form\Type\TourType;
use App\Manager\ImageManager;
use App\Manager\TourManager;
use App\Resource\Constant;
use App\Service\PaginationFactory;
use App\Service\StatusService;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TourController
 *
 * @Route("/tours")
 * @package App\Controller\Api
 */
class TourController extends BaseController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var TourManager
     */
    private $tourManager;

    /**
     * @var StatusService
     */
    private $statusService;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * TourController constructor.
     * @param TourManager $tourManager
     * @param StatusService $statusService
     * @param ImageManager $imageManager
     * @param PaginationFactory $paginationFactory
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct
    (
        TourManager $tourManager,
        StatusService $statusService,
        ImageManager $imageManager,
        PaginationFactory $paginationFactory,
        ViewHandlerInterface $viewHandler
    )
    {
        $this->tourManager = $tourManager;
        $this->statusService = $statusService;
        $this->imageManager = $imageManager;
        $this->paginationFactory = $paginationFactory;
        $this->viewHandler = $viewHandler;
    }

    /**
     * Get Tour
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_tour")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get Tour",
     *      @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Tour::class)
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Tour")
     * @param Tour $tour
     * @return Response|View
     **/
    public function retrieve(Tour $tour)
    {
        $view = $this->view($tour, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Get List Tours
     *
     * @Route("/", methods={"GET"}, name="app.get_tours")
     * @Security("has_role('ROLE_USER')")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the tours list ",
     *     @SWG\Schema(
     *         type="json",
     *         @Model(type=Tour::class, groups={"non_sensitive_data"})
     *     )
     * ),
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
     *  @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status value",
     *         type="integer",
     *  ),
     *  @SWG\Parameter(
     *         name="in_app_status",
     *         in="query",
     *         description="In App Status value",
     *         type="integer",
     *  ),
     *  @SWG\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Max price",
     *         type="number",
     *  ),
     *  @SWG\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Min price",
     *         type="number",
     *  ),
     *  @SWG\Parameter(
     *         name="country",
     *         in="query",
     *         description="filter by country",
     *         type="integer",
     *  ),
     *  @SWG\Parameter(
     *         name="city",
     *         in="query",
     *         description="filter by city",
     *         type="integer",
     *  ),
     * @SWG\Tag(name="Tour")
     * @param Request $request
     * @return mixed
     *
     **/
    public function listTours(Request $request)
    {
        $user =  $this->getUser();
        $status = $request->query->get('status', StatusService::ACTIVE);
        $maxPrice = $request->query->get('max_price', null);
        $minPrice = $request->query->get('min_price', null);
        $city = $request->query->get('city', null);
        $country = $request->query->get('country', null);
        $query = $request->query->get('query', null);
        $inAppStatus = $request->query->get('in_app_status', StatusService::PUBLISHED);

        $accessUser = $this->statusService->validate($user, $status, $inAppStatus);
        if (!$accessUser)
        {
            throw $this->createAccessDeniedException();
        }

        $tours = $this->tourManager->findAllList($query, $status, $inAppStatus, $minPrice, $maxPrice, $city, $country);
        $data = $this->paginationFactory->createCollection($tours, $request,"app.get_tours");
        $view = $this->view($data, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Create Tour
     *
     * @Route("/", methods={"POST"}, name="app.create_tour")
     *
     * @SWG\Parameter(
     * 		name="tour",
     * 		in="body",
     * 		required=true,
     *      @Model(type=TourType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=201,
     * 		description="success",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=TourType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Tour")
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     **/
    public function create(Request $request)
    {
        $form = $this->createForm(TourType::class, new Tour());
        $form->submit($request->request->all());

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Tour $data */
        $data = $form->getData();
        $data->setTranslates([$request->getLocale()]);
        $data->setOwner($this->getUser());
        $data->addTourLang($data, $request->getLocale());
        $tour = $this->statusService->changeStatus($data, Constant::PENDING);
        $this->tourManager->persist($tour);

        $view = $this->view($tour, 201);

        return $this->viewHandler->handle($view);
    }

    /**
     * Update Tour
     *
     * @Route("/{id}/", methods={"PUT"}, name="app.update_tour")
     *
     * @SWG\Parameter(
     * 		name="tour",
     * 		in="body",
     * 		required=true,
     *      @Model(type=TourType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Update Tour",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=TourType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Tour")
     * @param Request $request
     * @param Tour $tour
     *
     * @return mixed
     * @throws ValidationException
     **/
    public function update(Request $request, Tour $tour)
    {
        $form = $this->createForm(TourType::class, $tour);
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Tour $data */
        $data = $form->getData();
        $lang = $data->getTranslates();

        if(!in_array($request->getLocale(), $data->getTranslates())){
            array_push($lang, $request->getLocale());
            $data->setTranslates($lang);
        }

        $data->addTourLang($data, $request->getLocale());
        $tour = $this->statusService->changeStatus($data, Constant::PENDING);
        $this->tourManager->persist($tour);

        $view = $this->view($tour, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Add Tour images
     *
     * @Route("/{id}/images", methods={"PATCH"}, name="app.add_tour_images")
     *
     * @SWG\Parameter(
     * 		name="tour",
     * 		in="body",
     * 		required=true,
     *      @Model(type=ImageType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Update Tour",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=ImageType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Tour")
     * @param Request $request
     * @param Tour $tour
     *
     * @return mixed
     * @throws ValidationException
     **/
    public function addImages(Request $request, Tour $tour)
    {
        $form = $this->createForm(ImageType::class, new Image());
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Image $data */
        $data = $form->getData();
        $image = $this->imageManager->persist($data);

        $tour->addImages($image);
        $tour = $this->tourManager->persist($tour);

        $view = $this->view($tour, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Delete Tour
     * @Security("has_role('ROLE_USER')")
     * @Route("/{id}/", methods={"DELETE"}, name="app.delete_tour")
     *
     * @SWG\Response(
     * 		response=204,
     * 		description="Delete tour"
     * 	),
     *
     * @SWG\Tag(name="Tour")
     * @param Tour $tour
     *
     * @return mixed
     **/
    public function delete(Tour $tour)
    {
        $this->tourManager->remove($tour);

        $view = $this->view([], 204);

        return $this->viewHandler->handle($view);
    }

    /**
     * Activate tour.
     *
     * @Route("/{id}/activate", methods={"PUT"}, name="app.activate_tour")
     * @Security("has_role('ROLE_ADMIN')")
     * @SWG\Parameter(
     *     name="status",
     *     in="body",
     *     required=false,
     *     @Model(type=StatusActivateType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Tour confirmation for admin"
     * ),
     * @SWG\Tag(name="Tour")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param Tour $tour
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function activate(Tour $tour, Request $request)
    {
        $form = $this->createForm(StatusActivateType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        $data = $form->getData();

        /** @var Tour $tour */
        $tour = $this->statusService->changeStatus($tour, $data['status']);
        $this->tourManager->persist($tour);
        $view = $this->view($tour, 200);

        return $this->handleView($view);
    }

    /**
     * Publish tour.
     *
     * @Route("/{id}/publish", methods={"PUT"}, name="app.publish_tour")
     * @Security("has_role('ROLE_ADMIN')")
     * @SWG\Parameter(
     *     name="status",
     *     in="body",
     *     required=false,
     *     @Model(type=StatusActivateType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Tour publish for admin"
     * ),
     * @SWG\Tag(name="Tour")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param Tour $tour
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function publish(Tour $tour, Request $request)
    {
        $form = $this->createForm(StatusActivateType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }
        $data = $form->getData();

        /** @var Tour $tour */
        $tour = $this->statusService->changeAppStatus($tour, $data['status']);
        $this->tourManager->persist($tour);
        $view = $this->view($tour, 200);

        return $this->handleView($view);
    }

    /**
     * Get Next Experiences
     *
     * @Route("/my", methods={"GET"}, name="app.my_experiences")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the tours list ",
     *     @SWG\Schema(
     *         type="json",
     *         @Model(type=Tour::class)
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Tour")
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function myExperience()
    {
        $user = $this->getUser();
        $booking = $this->tourManager->myList($user);

        return $this->response($booking,200,"my_experiences");
    }
}

