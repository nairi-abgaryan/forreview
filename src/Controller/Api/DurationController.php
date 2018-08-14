<?php

namespace App\Controller\Api;

use App\Entity\Duration;
use App\Manager\DurationManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DurationController
 *
 * @Route("/durations")
 * @package App\Controller\Api
 */
class DurationController extends BaseController
{
    /**
     * @var DurationManager
     */
    private $durationManager;

    /**
     * DurationController constructor.
     * @param DurationManager $durationManager;
     */
    public function __construct(DurationManager $durationManager)
    {
        $this->durationManager = $durationManager;
    }

    /**
     * Get List Durations
     *
     * @Route("/", methods={"GET"}, name="app.get_durations")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Durations list ",
     *     @SWG\Schema(
     *         type="json",
     *         @Model(type=Duration::class, groups={"non_sensitive_data"})
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Duration")
     **/
    public function list()
    {
        $durations = $this->durationManager->findAll();

        return $this->response($durations, Response::HTTP_OK);
    }
}

