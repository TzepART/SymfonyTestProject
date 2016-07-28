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
        $arComments = [];
        $em = $this->getDoctrine()->getManager();
        $currentUserId = $this->getCurrentUserObject()->getId();

        $comments = $em->getRepository('TzepartChatBundle:Comments')->findAll();

        foreach ($comments as $index => $commentObj) {
            $arComments[$index]["id"] = $commentObj->getId();
            $arComments[$index]["text"] = $commentObj->getText();
            $arComments[$index]["create"] = $commentObj->getDateCreate()->format('d/m/Y H:i:s');;
            if($currentUserId == $commentObj->getUser()->getId()){
                $arComments[$index]["self"] = true;
            }else{
                $arComments[$index]["self"] = false;
            }
        }

        $arComments = array_reverse($arComments);

        return $this->render('TzepartChatBundle:Comments:index.html.twig', array(
            'comments' => $arComments,
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

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setUser($user);
            $comment->setText($request->get("commentText"));
            $comment->setDateCreate(new \DateTime('now'));
            $comment->setDateUpdate(new \DateTime('now'));
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('TzepartChatBundle:Comments:new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }
    
    
    /**
     * Deletes a Comments entity.
     *
     * @Route("/comment/{id}/delete", name="comment_delete")
     */
    public function deleteAction($id)
    {

        $em    = $this->getDoctrine()->getManager();
        $commentObj = $em->getRepository('TzepartChatBundle:Comments')->find($id);

        $em = $this->getDoctrine()->getManager();
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
