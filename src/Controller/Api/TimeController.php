<?php

namespace App\Controller\Api;

use App\Entity\Time;
use App\Manager\TimeManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class TimeController
 *
 * @Route("/times")
 * @package App\Controller\Api
 */
class TimeController extends FOSRestController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var TimeManager
     */
    private $TimeManager;

    /**
     * TimeController constructor.
     * @param TimeManager $TimeManager;
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(TimeManager $TimeManager, ViewHandlerInterface $viewHandler)
    {
        $this->TimeManager = $TimeManager;
        $this->viewHandler = $viewHandler;
    }

    /**
     * Get List Time
     *
     * @Route("/", methods={"GET"}, name="app.get_times")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the times list ",
     *     @SWG\Schema(
     *         type="json",
     *         @Model(type=Time::class, groups={"non_sensitive_data"})
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Time")
     **/
    public function list()
    {
        $times = $this->TimeManager->findAll();
        $view = $this->view($times, 200);

        return $this->viewHandler->handle($view);
    }
}

