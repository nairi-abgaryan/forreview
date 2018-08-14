<?php

namespace App\Controller\Api;

use App\Entity\Country;
use App\Manager\CountryManager;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;


/**
 * Class CountryController
 *
 * @Route("/country")
 * @package App\Controller\Api
 */
class CountryController extends BaseController
{
    /**
     * @var CountryManager
     */
    private $countryManager;

    /**
     * CountryController constructor.
     * @param CountryManager $countryManager;
     */
    public function __construct(CountryManager $countryManager)
    {
        $this->countryManager = $countryManager;
    }

    /**
     * Get Country
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_country")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get Country",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Country::class, groups={"default"})
     *     )
     * ),
     * @SWG\Tag(name="Country")
     * @SWG\Parameter(ref="#parameters/languages"),
     * @param Country $country
     * @return Response|View
     **/
    public function retrieve(Country $country)
    {
        return $this->response($country, Response::HTTP_OK);
    }

    /**
     * Get List Country
     *
     * @Route("/", methods={"GET"}, name="app.get_countries")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the countries list ",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Country::class, groups={"full"})
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Country")
     **/
    public function list()
    {
        $countries = $this->countryManager->findAll();

        return $this->response($countries, Response::HTTP_OK);
    }

    /**
     * Create Country
     *
     * @Route("/create", methods={"POST"}, name="app.create_countries")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the countries list ",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Country::class, groups={"full"})
     *     )
     * ),
     * @SWG\Tag(name="Country")
     **/
    public function create()
    {
        $yamlParser = new Yaml();
        $countries['en'] = $yamlParser->parseFile(__DIR__.'/../../../data/en.yml');
        $countries['ru'] = $yamlParser->parseFile(__DIR__.'/../../../data/ru.yml');
        $this->countryManager->persist($countries);

        return $this->response($countries, Response::HTTP_OK);
    }
}

