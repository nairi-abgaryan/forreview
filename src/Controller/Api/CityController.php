<?php

namespace App\Controller\Api;

use App\Entity\City;
use App\Manager\CityManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CityController
 *
 * @Route("/city")
 * @package App\Controller\Api
 */
class CityController extends BaseController
{
    /**
     * @var CityManager
     */
    private $cityManager;

    /**
     * CityController constructor.
     * @param CityManager $city;
     */
    public function __construct(CityManager $city)
    {
        $this->cityManager = $city;
    }

    /**
     * Get List City
     *
     * @Route("/", methods={"GET"}, name="app.get_city")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the cities list ",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=City::class, groups={"default"})
     *     )
     * ),
     * @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     type="string",
     *     required=false,
     *     description="Type for search in google or locale db",
     * ),
     * @SWG\Parameter(
     *     name="country",
     *     in="query",
     *     type="string",
     *     required=false,
     *     description="Country name",
     * )
     * @SWG\Parameter(
     *     name="city",
     *     in="query",
     *     type="string",
     *     required=false,
     *     description="City name",
     * )
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="City")
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $type = $request->query->get('type', null);
        $city = $request->query->get('city', null);
        $country = $request->query->get('country', null);
        $cities = $this->cityManager->search($type, $city, $country);

        return $this->response($cities, Response::HTTP_OK);
    }
}

