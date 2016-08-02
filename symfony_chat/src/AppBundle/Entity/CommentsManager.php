<?php
/**
 * Created by PhpStorm.
 * User: Ri
 * Date: 02.08.2016
 * Time: 1:17
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityManager;


class CommentsManager
{

    protected $em;
    protected $repository;

    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository($class);
    }

    function allComments()
    {
        $arComments = [];
        $comments   = $this->repository->findAll();
        foreach ($comments as $index => $commentObj) {
            $arComments[$index]["id"]     = $commentObj->getId();
            $arComments[$index]["userId"] = $commentObj->getUser()->getId();
            $arComments[$index]["text"]   = $commentObj->getText();
            $arComments[$index]["create"] = $commentObj->getDateCreate()->format('d/m/Y H:i:s');;
        }

        return $arComments;
    }

}