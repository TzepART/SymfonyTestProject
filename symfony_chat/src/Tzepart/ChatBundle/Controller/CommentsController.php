<?php

namespace Tzepart\ChatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Tzepart\ChatBundle\Entity\Comments;
use Tzepart\ChatBundle\Form\CommentsType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository('TzepartChatBundle:Comments')->findAll();

        return $this->render('TzepartChatBundle:Comments:index.html.twig', array(
            'comments' => $comments,
        ));
    }

    /**
     * Creates a new Comments entity.
     *
     * @Route("/comment/new", name="comment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $comment = new Comments();
        $form = $this->createForm('Tzepart\ChatBundle\Form\CommentsType', $comment);
        $form->handleRequest($request);
        $user = $this->getCurrentUserObject();


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setUser($user);
            $comment->setDateCreate(new \DateTime('now'));
            $comment->setDateUpdate(new \DateTime('now'));
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('comment_show', array('id' => $comment->getId()));
        }

        return $this->render('TzepartChatBundle:Comments:new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Comments entity.
     *
     * @Route("/comment/{id}", name="comment_show")
     * @Method("GET")
     */
    public function showAction(Comments $comment)
    {
        $deleteForm = $this->createDeleteForm($comment);

        return $this->render('TzepartChatBundle:Comments:show.html.twig', array(
            'comment' => $comment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Comments entity.
     *
     * @Route("/comment/{id}/edit", name="comment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Comments $comment)
    {
        $deleteForm = $this->createDeleteForm($comment);
        $editForm = $this->createForm('Tzepart\ChatBundle\Form\CommentsType', $comment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('comment_edit', array('id' => $comment->getId()));
        }

        return $this->render('TzepartChatBundle:Comments:edit.html.twig', array(
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Comments entity.
     *
     * @Route("/comment/{id}", name="comment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Comments $comment)
    {
        $form = $this->createDeleteForm($comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('comment_index');
    }

    /**
     * Creates a form to delete a Comments entity.
     *
     * @param Comments $comment The Comments entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comments $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_delete', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
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
            $arResult  = [];
            $commentId = $request->get("commentId");
            $newText = $request->get("newText");
            $em    = $this->getDoctrine()->getManager();
            $commentObj = $em->getRepository('TzepartChatBundle:Comments')->find($commentId);
            $commentObj->setText($newText);
            $commentObj->setDateUpdate(new \DateTime('now'));

            $em->persist($commentObj);
            $em->flush();
            $arResult["status"] = "Y";
            
            return new JsonResponse($arResult);
        }

        return new Response('This is not ajax!', 400);
    }


    /**
     * Get user object
     * @return integer $userId
     */
    protected function getCurrentUserObject()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return $user;
    }
}
