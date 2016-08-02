<?php

namespace ChatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ChatBundle\Entity\Comments;
use ChatBundle\Entity\CommentsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * Comments controller.
 *
 * @Route("/")
 */
class CommentsController extends Controller
{
    /**
     * Lists all Comments entities.
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
        $commentsManagerObj = new CommentsManager($em, 'ChatBundle:Comments');

        $arComments = array_reverse($commentsManagerObj->allComments());

        return [
            'comments' => $arComments,
            'friends' => $arFriends,
        ];
    }

    /**
     * Creates a new Comments entity.
     *
     * @Route("/comment/new", name="comment_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $comment = new Comments();
        $user    = $this->getCurrentUserObject();
        $form    = $this->createForm('ChatBundle\Form\CommentsType', $comment);
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
     * Deletes a Comments entity.
     *
     * @Route("/comment/{id}/delete", name="comment_delete")
     */
    public function deleteAction($id)
    {
        $em         = $this->getDoctrine()->getManager();
        $commentObj = $em->getRepository('ChatBundle:Comments')->find($id);

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
            $commentObj = $em->getRepository('ChatBundle:Comments')->find($commentId);
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
