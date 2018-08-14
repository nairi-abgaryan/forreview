<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;

class BaseController extends FOSRestController
{
    /**
     * @param $data
     * @param $statusCode
     * @param string $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response($data, $statusCode, $group = "default")
    {
        $context = new SerializationContext();
        $serializer = $this->container->get('jms_serializer');
        $context->setGroups([$group]);
        $data = $serializer->serialize($data, "json", $context);
        $data = $serializer->deserialize($data, "array", "json");
        $view = $this->view($data, $statusCode);

        return $this->handleView($view);
    }
}