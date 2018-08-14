<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Entity\User;
use App\Entity\Vendor;
use App\Exception\ValidationException;
use App\Form\Type\ImageType;
use App\Form\Type\StatusActivateType;
use App\Manager\ImageManager;
use App\Manager\VendorManager;
use App\Service\PaginationFactory;
use App\Service\StatusService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use App\Form\Type\VendorType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VendorController
 *
 * @Route("/vendors")
 * @package App\Controller\Api
 */
class VendorController extends FOSRestController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var VendorManager
     */
    private $vendorManager;

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
     * VendorController constructor.
     * @param VendorManager $vendorManager
     * @param StatusService $statusService
     * @param ImageManager $imageManager
     * @param PaginationFactory $paginationFactory
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct
    (
        VendorManager $vendorManager,
        StatusService $statusService,
        ImageManager $imageManager,
        PaginationFactory $paginationFactory,
        ViewHandlerInterface $viewHandler
    )
    {
        $this->vendorManager = $vendorManager;
        $this->statusService = $statusService;
        $this->paginationFactory = $paginationFactory;
        $this->imageManager = $imageManager;
        $this->viewHandler = $viewHandler;
    }

    /**
     * Get Vendor
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_vendor")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get Vendor"
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor")
     * @param Vendor $vendor
     * @return Response|View
     **/
    public function retrieve(Vendor $vendor)
    {
        $view = $this->view($vendor, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Get List Vendors
     *
     * @Route("/", methods={"GET"}, name="app.get_vendors")
     * @Security("has_role('ROLE_USER')")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the vendors list ",
     *     @SWG\Schema(
     *         type="json",
     *         @Model(type=Vendor::class, groups={"non_sensitive_data"})
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
     *         name="activity",
     *         in="query",
     *         description="Tour type query parameter for filtering vendor list",
     *         type="integer",
     *  ),
     *  @SWG\Parameter(
     *         name="vendor_type",
     *         in="query",
     *         description="Vendor type query parameter for filtering vendor list",
     *         type="string",
     *  ),
     *  @SWG\Parameter(
     *         name="city",
     *         in="query",
     *         description="Vendor filter by city",
     *         type="string",
     *  ),
     * @SWG\Parameter(
     *         name="country",
     *         in="query",
     *         description="Vendor filter by country",
     *         type="string",
     *  )
     * @SWG\Tag(name="Vendor")
     * @param Request $request
     * @return mixed
     *
     **/
    public function search(Request $request)
    {
        /** @var User $user */
        $user =  $this->getUser();
        $query = $request->query->get('query', null);
        $city = $request->query->get('city', null);
        $country = $request->query->get('country', null);
        $activity = $request->query->get('activity', null);
        $vendorType = $request->query->get('vendor_type', null);
        $status = $request->query->get('status', StatusService::ACTIVE);
        $inAppStatus = $request->query->get('in_app_status', StatusService::PUBLISHED);

        $accessUser = $this->statusService->validate($user, $status, $inAppStatus);
        if (!$accessUser) {
            throw $this->createAccessDeniedException();
        }

        $vendorsTranslate = $this->vendorManager->search($query, $status, $inAppStatus, $activity, $vendorType, $country, $city);
        $data = $this->paginationFactory->createCollection($vendorsTranslate, $request,"app.get_vendors");
        $view = $this->view($data, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Create Vendor
     *
     * @Route("/", methods={"POST"}, name="app.create_vendor")
     *
     * @SWG\Parameter(
     * 		name="vendor",
     * 		in="body",
     * 		required=true,
     *      @Model(type=VendorType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=201,
     * 		description="success",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=VendorType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor")
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     **/
    public function create(Request $request)
    {
        $form = $this->createForm(VendorType::class, new Vendor());
        $form->submit($request->request->all());

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Vendor $data */
        $data = $form->getData();
        $vendor = $this->vendorManager->createVendor($data, $request->getLocale());
        $view = $this->view($vendor, 201);

        return $this->viewHandler->handle($view);
    }

    /**
     * Update Vendor
     *
     * @Route("/{id}/", methods={"PUT"}, name="app.update_vendor")
     *
     * @SWG\Parameter(
     * 		name="vendor",
     * 		in="body",
     * 		required=true,
     *      @Model(type=VendorType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Update Vendor",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=VendorType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor")
     * @param Request $request
     * @param Vendor $vendor
     *
     * @return mixed
     * @throws ValidationException
     **/
    public function update(Request $request, Vendor $vendor)
    {
        $form = $this->createForm(VendorType::class, $vendor);
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Vendor $data */
        $data = $form->getData();
        $vendor = $this->vendorManager->createVendor($data, $request->getLocale());
        $view = $this->view($vendor, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Add Vendor images
     *
     * @Route("/{id}/images", methods={"PATCH"}, name="app.add_vendor_images")
     *
     * @SWG\Parameter(
     * 		name="vendor",
     * 		in="body",
     * 		required=true,
     *      @Model(type=ImageType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Update Vendor",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=ImageType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor")
     * @param Request $request
     * @param Vendor $vendor
     *
     * @return mixed
     * @throws ValidationException
     **/
    public function addImages(Request $request, Vendor $vendor)
    {
        $form = $this->createForm(ImageType::class, new Image());
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Image $data */
        $data = $form->getData();
        $image = $this->imageManager->persist($data);

        $vendor->addImages($image);
        $vendor = $this->vendorManager->persist($vendor);

        $view = $this->view($vendor, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Delete Vendor
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/", methods={"DELETE"}, name="app.delete_vendor")
     *
     * @SWG\Response(
     * 		response=204,
     * 		description="Delete vendor"
     * 	),
     *
     * @SWG\Tag(name="Vendor")
     * @param Vendor $vendor
     *
     * @return mixed
     **/
    public function delete(Request $request,Vendor $vendor)
    {
        $request->headers->all();
        $this->vendorManager->remove($vendor);

        $view = $this->view([], 204);

        return $this->viewHandler->handle($view);
    }

    /**
     * Activate Vendor.
     *
     * @Route("/{id}/activate", methods={"PUT"}, name="app.activate_vendor")
     * @Security("has_role('ROLE_ADMIN')")
     * @SWG\Parameter(
     *     name="status",
     *     in="body",
     *     required=false,
     *     @Model(type=StatusActivateType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Vendor confirmation for admin"
     * ),
     * @SWG\Tag(name="Vendor")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param Vendor $vendor
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function activate(Vendor $vendor, Request $request)
    {
        $form = $this->createForm(StatusActivateType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }
        $data = $form->getData();

        /** @var Vendor $vendor */
        $vendor = $this->statusService->changeStatus($vendor, $data['status']);
        $this->vendorManager->persist($vendor);
        $view = $this->view($vendor, 200);

        return $this->handleView($view);
    }

    /**
     * Publish vendor.
     *
     * @Route("/{id}/publish", methods={"PUT"}, name="app.publish_vendor")
     * @Security("has_role('ROLE_ADMIN')")
     * @SWG\Parameter(
     *     name="status",
     *     in="body",
     *     required=false,
     *     @Model(type=StatusActivateType::class, groups={"list"})
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Vendor publish for admin"
     * ),
     * @SWG\Tag(name="Vendor")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param Vendor $vendor
     * @param Request $request
     * @return Response|View
     * @return Response|static
     * @throws \Exception
     */
    public function publish(Vendor $vendor, Request $request)
    {
        $form = $this->createForm(StatusActivateType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }
        $data = $form->getData();

        /** @var Vendor $vendor */
        $vendor = $this->statusService->changeAppStatus($vendor, $data['status']);
        $this->vendorManager->persist($vendor);
        $view = $this->view($vendor, 200);

        return $this->handleView($view);
    }
}

