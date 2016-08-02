<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Comment;
use AppBundle\Entity\CommentManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * Comment controller.
 *
 * @Route("/")
 */
class CommentController extends Controller
{
    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="comment_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $arFriends = [];

        if (!empty($_SESSION['user_friends'])) {
            $arFriends = $_SESSION['user_friends'];
        }

        $em                 = $this->getDoctrine()->getManager();
        $commentsManagerObj = new CommentManager($em, 'AppBundle:Comment');

        $arComments = array_reverse($commentsManagerObj->allComments());

        return [
            'comments' => $arComments,
            'friends' => $arFriends,
        ];
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/comment/new", name="comment_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $comment = new Comment();
        $user    = $this->getCurrentUserObject();
        $form    = $this->createForm('AppBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setUser($user);
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('comment_index');
        }

        return [
            'comment' => $comment,
            'form' => $form->createView(),
        ];
    }


    /**
     * Deletes a Comment entity.
     *
     * @Route("/comment/{id}/delete", name="comment_delete")
     */
    public function deleteAction($id)
    {
        $em         = $this->getDoctrine()->getManager();
        $commentObj = $em->getRepository('AppBundle:Comment')->find($id);

        $em->remove($commentObj);
        $em->flush();

        return $this->redirectToRoute('comment_index');
    }


    /**
     *
     * @Route("/comment/editAjax", name="comment_edit_ajax")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */

    public function editAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $arResult   = [];
            $commentId  = $request->get("commentId");
            $newText    = $request->get("newText");
            $em         = $this->getDoctrine()->getManager();
            $commentObj = $em->getRepository('AppBundle:Comment')->find($commentId);
            $commentObj->setText($newText);
            $commentObj->setDateUpdate(new \DateTime('now'));

            $em->persist($commentObj);
            $em->flush();
            $arResult["status"] = "OK";

            return new JsonResponse($arResult, 201);
        }

        return new Response('This is not ajax!', 400);
    }


    /**
     * Get user object
     * @return integer $userId
     */
    protected function getCurrentUserObject()
    {
        $user = $this->getUser();

        return $user;
    }
}
