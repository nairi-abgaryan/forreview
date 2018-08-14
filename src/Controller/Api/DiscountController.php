<?php

namespace App\Controller\Api;

use App\Entity\Discount;
use App\Manager\DiscountManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DiscountController
 *
 * @Route("/discount")
 * @package App\Controller\Api
 */
class DiscountController extends BaseController
{
    /**
     * @var DiscountManager
     */
    private $discountManager;

    /**
     * DiscountController constructor.
     * @param DiscountManager $discountManager;
     */
    public function __construct(DiscountManager $discountManager)
    {
        $this->discountManager = $discountManager;
    }

    /**
     * Get List Discounts
     *
     * @Route("/", methods={"GET"}, name="app.get_discounts")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Discounts list ",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Discount::class)
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Discount")
     **/
    public function list()
    {
        $discounts = $this->discountManager->findAll();

        return $this->response($discounts, Response::HTTP_OK);
    }
}

