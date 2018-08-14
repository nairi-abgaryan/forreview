<?php

namespace App\Controller\Api;

use App\Exception\ValidationException;
use App\Manager\ImageManager;
use App\Manager\VendorTypeManager;
use App\Service\PaginationFactory;
use App\Service\StatusService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\VendorType;
use App\Form\Type\VendorTypeType;

/**
 * Class VendorTypeController
 *
 * @Route("/vendor-type")
 * @package App\Controller\Api
 */
class VendorTypeController extends FOSRestController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var VendorTypeManager
     */
    private $vendorTypeManager;

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
     * @param ViewHandlerInterface $viewHandler
     * @param VendorTypeManager $vendorTypeManager
     * @param ImageManager $imageManager
     * @param PaginationFactory $paginationFactory
     * @param StatusService $statusService
     */
    public function __construct
    (
        ViewHandlerInterface $viewHandler,
        VendorTypeManager $vendorTypeManager,
        ImageManager $imageManager,
        PaginationFactory $paginationFactory,
        StatusService $statusService
    )
    {
        $this->viewHandler = $viewHandler;
        $this->vendorTypeManager = $vendorTypeManager;
        $this->imageManager = $imageManager;
        $this->paginationFactory = $paginationFactory;
        $this->statusService = $statusService;
    }

    /**
     * Get Vendor Type
     *
     * @Route("/{id}/", methods={"GET"}, name="app.vendor_type")
     * @SWG\Response(
     *     response=200,
     *     description="Get Vendor Type"
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor Type")
     * @param VendorType $vendorType
     * @return Response|View
     **/
    public function retrieve(VendorType $vendorType)
    {
        $view = $this->view($vendorType, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Get Vendor Type List
     *
     * @Route("/", methods={"GET"}, name="app.vendor_types")
     * @SWG\Response(
     *     response=200,
     *     description="Get Vendor Type List"
     * ),
     * @SWG\Parameter(
     *     name="in_app_status",
     *     in="query",
     *     description="In App Status value",
     *     type="integer",
     *  ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor Type")
     * @param Request $request
     * @return Response|View
     **/
    public function list(Request $request)
    {
        $user  =  $this->getUser();
        $query = $request->query->get('query', null);
        $inAppStatus = $request->query->get('in_app_status', StatusService::PUBLISHED);
        $accessUser = $this->statusService->validateAppStatus($user, $inAppStatus);
        if (!$accessUser) {
            throw $this->createAccessDeniedException();
        }

        $data = $this->vendorTypeManager->search($query, $inAppStatus);
        $view = $this->view($data, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Create Vendor Type
     *
     * @Route("/", methods={"POST"}, name="app.create_vendor_type")
     *
     * @SWG\Parameter(
     * 		name="vendorType",
     * 		in="body",
     * 		required=true,
     *      @Model(type=VendorTypeType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=201,
     * 		description="success",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=VendorTypeType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor Type")
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     **/
    public function create(Request $request)
    {
        $form = $this->createForm(VendorTypeType::class, new VendorType());
        $form->submit($request->request->all(), false);
        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var VendorType $data */
        $data = $form->getData();
        $data->setTranslates([$request->getLocale()]);
        $data->addVendorTypeLang($data, $request->getLocale());
        $this->vendorTypeManager->persist($data);

        $view = $this->view($data, 201);

        return $this->viewHandler->handle($view);
    }

    /**
     * Update Vendor Type
     *
     * @Route("/{id}/", methods={"PUT"}, name="app.update_vendor_type")
     *
     * @SWG\Parameter(
     * 		name="vendorType",
     * 		in="body",
     * 		required=true,
     *      @Model(type=VendorTypeType::class, groups={"full"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="success",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=VendorTypeType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor Type")
     * @param Request $request
     * @param VendorType $vendorType
     *
     * @return mixed
     * @throws ValidationException
     **/
    public function update(Request $request, VendorType $vendorType)
    {
        $form = $this->createForm(VendorTypeType::class, $vendorType);
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var VendorType $data */
        $data = $form->getData();
        $lang = $data->getTranslates();

        if(!in_array($request->getLocale(), $data->getTranslates())){
            array_push($lang, $request->getLocale());
            $data->setTranslates($lang);
        }

        $data->addVendorTypeLang($data, $request->getLocale());
        $this->vendorTypeManager->persist($data);
        $vendorType = $this->vendorTypeManager->persist($data);

        $view = $this->view($vendorType, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Delete Vendor Type
     *
     * @Route("/{id}/", methods={"DELETE"}, name="app.delete_vendor_type")
     *
     * @SWG\Response(
     * 		response=204,
     * 		description="Delete Vendor Type ",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=VendorTypeType::class, groups={"full"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Vendor Type")
     * @param VendorType $vendorType
     *
     * @return mixed
     **/
    public function delete(VendorType $vendorType)
    {
        $this->vendorTypeManager->remove($vendorType);

        $view = $this->view([], 204);

        return $this->viewHandler->handle($view);
    }
}

