<?php

namespace App\Controller\Api;

use App\Manager\RoleManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 *
 * @Route("/roles")
 * @package App\Controller\Api
 */
class RoleController extends FOSRestController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * VendorController constructor.
     * @param RoleManager $roleManager
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(RoleManager $roleManager, ViewHandlerInterface $viewHandler)
    {
        $this->roleManager = $roleManager;
        $this->viewHandler = $viewHandler;
    }

    /**
     * Get Roles
     *
     * @Route("/", methods={"GET"}, name="app.get_roles")
     * @SWG\Response(
     *     response=200,
     *     description="Get Roles"
     * ),
     * @SWG\Tag(name="Roles")
     * @return Response|View
     **/
    public function retrieve()
    {
        $roles = $this->roleManager->findBy(["type" => true]);
        $view = $this->view($roles, 200);

        return $this->viewHandler->handle($view);
    }
}
