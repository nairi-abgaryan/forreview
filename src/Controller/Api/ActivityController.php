<?php

namespace App\Controller\Api;

use App\Entity\Activity;
use App\Exception\ValidationException;
use App\Form\Type\ActivityType;
use App\Manager\ActivityManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ActivityController
 *
 * @Route("/activities")
 * @package App\Controller\Api
 */
class ActivityController extends BaseController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var ActivityManager
     */
    private $activityManager;

    /**
     * ActivityController constructor.
     * @param ViewHandlerInterface $viewHandler
     * @param ActivityManager $activityManager
     */
    public function __construct
    (
        ViewHandlerInterface $viewHandler,
        ActivityManager $activityManager
    )
    {
        $this->viewHandler = $viewHandler;
        $this->activityManager = $activityManager;
    }

    /**
     * Get Activity
     *
     * @Route("/{id}/", methods={"GET"}, name="app.get_activity")
     * @SWG\Response(
     *     response=200,
     *     description="Activity by id",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Activity::class, groups={"default"})
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Activity")
     * @param Activity $activity
     * @return Response|View
     **/
    public function retrieve(Activity $activity)
    {
        return $this->response($activity, Response::HTTP_OK);
    }

    /**
     * Get List Activities
     *
     * @Route("/", methods={"GET"}, name="app.get_activities")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned activities list",
     *     @SWG\Schema(
     *         type="json",
     *         @Model(type=Activity::class, groups={"default"})
     *     )
     * ),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Activity")
     **/
    public function getActivities()
    {
        $activities = $this->activityManager->findAll();

        return $this->response($activities, Response::HTTP_OK);
    }

    /**
     * Create Activity
     *
     * @Route("/", methods={"POST"}, name="app.create_activity")
     *
     * @SWG\Parameter(
     * 		name="activity",
     * 		in="body",
     * 		required=true,
     *      @Model(type=ActivityType::class, groups={"default"})
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Activity by id",
     *     @SWG\Schema(
     *         type="json",
     *         ref=@Model(type=Activity::class, groups={"default"})
     *     )
     * ),
     * @SWG\Response(
     * 		response=400,
     * 		description="Validation failed"
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Activity")
     * @param Request $request
     * @return mixed
     * @return Response
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $form = $this->createForm(ActivityType::class, new Activity());
        $form->submit($request->request->all(), false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Activity $data */
        $data = $form->getData();
        $activity = $this->activityManager->createActivity($data, $request->getLocale());

        return $this->response($activity,Response::HTTP_CREATED);
    }

    /**
     * Update Activity Translate
     *
     * @Route("/{id}/", methods={"PUT"}, name="app.update_activities")
     * @SWG\Parameter(
     * 		name="activity",
     * 		in="body",
     * 		required=true,
     *      @Model(type=ActivityType::class, groups={"default"})
     * ),
     * @SWG\Response(
     * 		response=200,
     * 		description="Activity update Success",
     * 		@SWG\Schema(
     *         type="array",
     *         @Model(type=Activity::class, groups={"default"})
     *      )
     * 	),
     * @SWG\Response(
     * 		response=400,
     * 		description="Validation failed"
     * 	),
     * @SWG\Parameter(ref="#parameters/languages"),
     * @SWG\Tag(name="Activity")
     * @param Request $request
     * @param Activity $activity
     *
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, Activity $activity)
    {
        $form = $this->createForm(ActivityType::class,  $activity);
        $form->submit($request->request->all(),false);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Activity $data */
        $data = $form->getData();
        $activity = $this->activityManager->updateActivity($data, $request->getLocale());

        return $this->response($activity, Response::HTTP_OK);
    }

    /**
     * Delete Activity
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/", methods={"DELETE"}, name="app.delete_activity")
     *
     * @SWG\Response(
     * 		response=204,
     * 		description="Activity"
     * 	),
     * @SWG\Tag(name="Activity")
     * @param Activity $activity
     *
     * @return mixed
     **/
    public function delete(Activity $activity)
    {
        $this->activityManager->remove($activity);
        return $this->response([], Response::HTTP_NO_CONTENT);
    }
}

