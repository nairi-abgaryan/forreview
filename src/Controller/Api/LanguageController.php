<?php

namespace App\Controller\Api;

use App\Entity\Language;
use App\Manager\LanguageManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Language
 * @Route("/languages")
 * @package App\Controller\Api
 */
class LanguageController extends FOSRestController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var LanguageManager
     */
    private $languageManager;

    /**
     * Language constructor.
     * @param ViewHandlerInterface $viewHandler
     * @param LanguageManager $languageManager
     */
    public function __construct(ViewHandlerInterface $viewHandler, LanguageManager $languageManager)
    {
        $this->viewHandler = $viewHandler;
        $this->languageManager = $languageManager;
    }

    /**
     * Get Language  Translate
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_Language")
     * @SWG\Response(
     *     response=200,
     *     description="Get Language",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Language::class)
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Language")
     * @param Language $language
     * @return Response|View
     **/
    public function retrieve(Language $language)
    {
        $view = $this->view($language, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Get Language List
     *
     * @Route("/", methods={"GET"}, name="app.get_Language_list")
     * @SWG\Response(
     *     response=200,
     *     description="Get Language  List",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Language::class)
     *     )
     * )
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Language")
     * @return Response|View
     **/
    public function list()
    {
        $data = $this->languageManager->findAll();
        $view = $this->view($data, 200);

        return $this->viewHandler->handle($view);
    }

    /**
     * Create Language
     *
     * @Route("/", methods={"POST"}, name="app.create_Language")
     * @SWG\Response(
     * 		response=201,
     * 		description="success"
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Language")
     * @return mixed
     **/
    public function create()
    {
        $langJson = file_get_contents(__DIR__.'/../../../data/langauges.json');
        $languages['en'] = json_decode($langJson,true);
        $data = $this->languageManager->persist($languages);

        $view = $this->view($data, 201);

        return $this->viewHandler->handle($view);
    }
}

