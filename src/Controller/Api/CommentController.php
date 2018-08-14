<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\Tour;
use App\Exception\ValidationException;
use App\Form\Type\CommentType;
use App\Manager\CommentManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 *
 * @Route("/comments")
 * @package App\Controller\Api
 */
class CommentController extends BaseController
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CommentManager
     */
    private $commentManager;

    /**
     * CommentController constructor.
     * @param ViewHandlerInterface $viewHandler
     * @param CommentManager $commentManager
     */
    public function __construct
    (
        ViewHandlerInterface $viewHandler,
        CommentManager $commentManager
    )
    {
        $this->viewHandler = $viewHandler;
        $this->commentManager = $commentManager;
    }

    /**
     * Create Comment
     *
     * @Route("/", methods={"POST"}, name="app.create_comment")
     *
     * @SWG\Parameter(
     *     name="rate",
     *     in="body",
     *     required=true,
     *     @Model(type=CommentType::class)
     * ),
     * @SWG\Response(
     * 		response=400,
     * 		description="Validation failed"
     * 	),
     * @SWG\Response(
     * 		response=200,
     * 		description="Creation success"
     * 	),
     * @SWG\Tag(name="Comments")
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     **/
    public function create(Request $request)
    {
        $form = $this->createForm(CommentType::class);
        $form->submit($request->request->all(), true);

        if (!$form->isValid()) {
            throw new ValidationException($form);
        }

        /** @var Comment $data */
        $data = $form->getData();
        $comment = $this->commentManager->createComment($data, $this->getUser());

        if (!$comment){
            throw $this->createAccessDeniedException();
        }

        return $this->view([],Response::HTTP_CREATED);
    }

    /**
     * Get List Comments
     *
     * @Route("/{tour}", methods={"GET"}, name="app.get_comments")
     * @SWG\Response(
     * 		response=200,
     * 		description="Creation success"
     * 	),
     * @SWG\Tag(name="Comments")
     * @param Tour $tour
     * @return View
     */
    public function getComments(Tour $tour)
    {
        $comments = $this->commentManager->findOneBy(["tour" => $tour]);

        return $this->view($comments,Response::HTTP_CREATED);
    }
}

