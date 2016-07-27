<?php
/**
 * Created by PhpStorm.
 * User: Ri
 * Date: 27.07.2016
 * Time: 19:41
 */

namespace Tzepart\ChatBundle\Controller;

use FOS\UserBundle\Controller\SecurityController;


class ChatSecurityController extends SecurityController
{
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('security/login.html.twig', $data);
    }

}