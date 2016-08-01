<?php

namespace Tzepart\ChatBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;


class SecurityController extends BaseSecurityController
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
        return $this->render('security\login.html.twig', $data);
    }

}