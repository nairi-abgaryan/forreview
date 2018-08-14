<?php

namespace App\Controller\Api;

use App\Entity\Booking;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Form\Type\BookingType;
use App\Form\Type\TourTimeType;
use App\Manager\BookingManager;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookingController
 *
 * @Route("/booking")
 * @package App\Controller\Api
 */
class BookingController extends BaseController
{
    /**
     * @var BookingManager
     */
    private $bookingManager;

    /**
     * BookingController constructor.
     * @param BookingManager $bookingManager
     */
    public function __construct(BookingManager $bookingManager)
    {
        $this->bookingManager = $bookingManager;
    }

    /**
     * Get Booking
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_booking")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get Booking",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Booking::class)
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Booking")
     * @param Booking $booking
     * @return Response|View
     **/
    public function retrieve(Booking $booking)
    {
        return $this->response($booking, Response::HTTP_OK, "full");
    }

    /**
     * Get List Bookings
     * @Route("/", methods={"GET"}, name="app.get_bookings")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the bookings list",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Booking::class)
     *     )
     * ),
     * @SWG\Parameter(
     *         name="expertStatus",
     *         in="query",
     *         description="Status for experts",
     *         type="integer"
     *  ),
     * @SWG\Parameter(
     *         name="touristStatus",
     *         in="query",
     *         description="Status for experts",
     *         type="integer",
     *  ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Booking")
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $user = $this->getUser();
        $expertStatus = $request->query->get('expertStatus', null);
        $touristStatus = $request->query->get('touristStatus', null);
        $booking = $this->bookingManager->search($user, $expertStatus, $touristStatus);

        return $this->response($booking, Response::HTTP_OK);
    }

    /**
     * Get My Bookings
     * @Route("/my", methods={"GET"}, name="app.get_my_bookings")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns my bookings list",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Booking::class)
     *     )
     * )\
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Booking")
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function myList()
    {
        $user = $this->getUser();
        $booking = $this->bookingManager->myList($user);

        return $this->response($booking, Response::HTTP_OK, "trips");
    }

    /**
     * Create Booking
     *
     * @Route("/", methods={"POST"}, name="app.create_booking")
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Parameter(
     * 		name="booking",
     * 		in="body",
     * 		required=true,
     *      @Model(type=BookingType::class)
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Returns the bookings list ",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Booking::class, groups={"default"})
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Booking")
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     **/
    public function create(Request $request)
    {
        $form = $this->createForm(BookingType::class, new Booking());
        $form->submit($request->request->all(), false);
        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Booking $data */
        $data = $form->getData();
        $this->bookingManager->createBooking($data, $this->getUser());

        return $this->response($data, Response::HTTP_OK);
    }

    /**
     * Update Booking
     *
     * @Route("/", methods={"PUT"}, name="app.update_booking")
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Parameter(
     * 		name="booking",
     * 		in="body",
     * 		required=true,
     *      @Model(type=BookingType::class)
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Booking Update success",
     * 		@SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Booking::class, groups={"default"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Booking")
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     **/
    public function update(Request $request)
    {
        $form = $this->createForm(BookingType::class, new Booking());
        $form->submit($request->request->all(), false);
        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Booking $data */
        $data = $form->getData();
        $this->bookingManager->persist($data);

        return $this->response($data, Response::HTTP_CREATED);
    }

    /**
     * Cancel Booking
     *
     * @Route("/{id}", methods={"DELETE"}, name="app.delete_booking")
     * @Security("has_role('ROLE_USER')")
     * @SWG\Parameter(
     * 		name="booking",
     * 		in="body",
     * 		required=true,
     *      @Model(type=BookingType::class)
     * ),
     * @SWG\Response(
     * 		response=204,
     * 		description="success"
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Booking")
     * @param Booking $booking
     * @return mixed
     **/
    public function reject(Booking $booking)
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->bookingManager->reject($booking, $user);

        return $this->response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Invite tourist set time for tour
     *
     * @Route("/{id}", methods={"PATCH"}, name="app.invite_booking")
     * @Security("has_role('ROLE_EXPERT')")
     *
     * @SWG\Parameter(
     * 		name="booking",
     * 		in="body",
     * 		required=true,
     *      @Model(type=TourTimeType::class)
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Booking Update success",
     * 		@SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Booking::class, groups={"default"})
     *      )
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Booking")
     * @param Request $request
     * @param Booking $booking
     * @return mixed
     * @throws ValidationException
     **/
    public function invitation(Request $request, Booking $booking)
    {
        $form = $this->createForm(TourTimeType::class, $booking);
        $form->submit($request->request->all(), false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        $booking = $this->bookingManager->invite($booking);

        return $this->response($booking, Response::HTTP_OK);
    }
}
