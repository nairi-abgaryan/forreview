<?php

namespace App\Controller;

use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * Ios Deep linking url
     *
     * @Route("/apple-app-site-association", name="api.ios_deep_linking")
     * @Method({"GET"})
     *
     * @return Response|View
     */
    public function deepLinking( )
    {
        $content = file_get_contents(__DIR__."/../../data/ios-deep-linking.json");
        $response = JsonResponse::fromJsonString($content);

        return $response;
    }
}
