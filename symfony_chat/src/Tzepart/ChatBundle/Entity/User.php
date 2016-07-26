<?php
/**
 * Created by PhpStorm.
 * User: Ri
 * Date: 27.07.2016
 * Time: 0:17
 */

namespace Tzepart\ChatBundle\Security\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Tzepart\ChatBundle\Repository\UserRepository")
 * @ORM\Table(name="lcl_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;
    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;
    /** @ORM\Column(name="username", type="string", length=255, nullable=true) */
    protected $username;
    /** @ORM\Column(name="secondname", type="string", length=255, nullable=true) */
    protected $secondname;



    //YOU CAN ADD MORE CODE HERE !
}