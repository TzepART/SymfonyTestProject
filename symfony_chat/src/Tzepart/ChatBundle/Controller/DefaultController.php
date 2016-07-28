<?php

namespace Tzepart\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use HWI\Bundle\OAuthBundle\Controller\ConnectController;

class DefaultController extends ConnectController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('TzepartChatBundle:Default:index.html.twig');
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
